<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use PHPUnit\Framework\TestCase;

class GenerateTestDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:document 
                            {--suite=Feature : Test suite to document (Feature|Unit)}
                            {--output=docs/tests : Output directory for documentation}
                            {--format=md : Output format (md|html)}
                            {--template=default : Template to use}';

    /**
     * The console command description.
     */
    protected $description = 'Generate markdown documentation from test files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $suite = $this->option('suite');
        $outputDir = $this->option('output');
        $format = $this->option('format');
        
        $this->info("Generating test documentation for {$suite} suite...");
        
        // Crear directorio de salida si no existe
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
            $this->info("Created output directory: {$outputDir}");
        }
        
        // Obtener archivos de test
        $testFiles = $this->getTestFiles($suite);
        
        if (empty($testFiles)) {
            $this->warn("No test files found in {$suite} suite");
            return Command::FAILURE;
        }
        
        $this->info("Found " . count($testFiles) . " test files");
        
        $progressBar = $this->output->createProgressBar(count($testFiles));
        $progressBar->start();
        
        foreach ($testFiles as $testFile) {
            $this->processTestFile($testFile, $outputDir);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Generar índice general
        $this->generateIndex($outputDir, $testFiles);
        
        $this->info("✅ Documentation generated successfully in: {$outputDir}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Obtener archivos de test del suite especificado
     */
    private function getTestFiles(string $suite): array
    {
        $testPath = base_path("tests/{$suite}");
        
        if (!File::exists($testPath)) {
            return [];
        }
        
        return File::allFiles($testPath);
    }
    
    /**
     * Procesar archivo de test individual
     */
    private function processTestFile($testFile, string $outputDir): void
    {
        $className = $this->getClassNameFromFile($testFile);
        
        if (!$className) {
            return;
        }
        
        try {
            $reflection = new ReflectionClass($className);
            $testMethods = $this->getTestMethods($reflection);
            
            if (empty($testMethods)) {
                return;
            }
            
            $documentation = $this->generateDocumentationForClass($reflection, $testMethods);
            
            $fileName = str_replace('Test', '', $reflection->getShortName());
            $fileName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $fileName));
            $filePath = "{$outputDir}/{$fileName}.md";
            
            File::put($filePath, $documentation);
            
        } catch (\Exception $e) {
            $this->warn("Error processing {$testFile->getFilename()}: " . $e->getMessage());
        }
    }
    
    /**
     * Extraer nombre de clase del archivo
     */
    private function getClassNameFromFile($file): ?string
    {
        $content = File::get($file->getPathname());
        
        // Extraer namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';
        
        // Extraer nombre de clase
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[1] ?? '';
        
        return $namespace ? "{$namespace}\\{$className}" : $className;
    }
    
    /**
     * Obtener métodos de test de la clase
     */
    private function getTestMethods(ReflectionClass $class): array
    {
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        
        return array_filter($methods, function($method) {
            return strpos($method->getName(), 'test') === 0 || 
                   $this->hasTestAnnotation($method);
        });
    }
    
    /**
     * Verificar si el método tiene anotación @test
     */
    private function hasTestAnnotation(ReflectionMethod $method): bool
    {
        $docComment = $method->getDocComment();
        return $docComment && strpos($docComment, '@test') !== false;
    }
    
    /**
     * Generar documentación para una clase de test
     */
    private function generateDocumentationForClass(ReflectionClass $class, array $testMethods): string
    {
        $className = $class->getShortName();
        $fileName = $class->getFileName();
        $relativeFileName = str_replace(base_path(), '', $fileName);
        
        $doc = "# Documentación de Tests: {$className}\n\n";
        $doc .= "## Información General\n";
        $doc .= "- **Archivo**: `{$relativeFileName}`\n";
        $doc .= "- **Fecha de generación**: " . now()->format('Y-m-d H:i:s') . "\n";
        $doc .= "- **Total de tests**: " . count($testMethods) . "\n\n";
        $doc .= "---\n\n";
        
        $testCounter = 1;
        
        foreach ($testMethods as $method) {
            $doc .= $this->generateTestMethodDoc($method, $testCounter);
            $testCounter++;
        }
        
        $doc .= $this->generateTestSummary($className, count($testMethods));
        
        return $doc;
    }
    
    /**
     * Generar documentación para un método de test
     */
    private function generateTestMethodDoc(ReflectionMethod $method, int $counter): string
    {
        $methodName = $method->getName();
        $docComment = $method->getDocComment();
        
        // Extraer información del docblock
        $purpose = $this->extractFromDocblock($docComment, 'purpose') ?: 
                  $this->generatePurposeFromMethodName($methodName);
        $expectedResult = $this->extractFromDocblock($docComment, 'expectedResult') ?: 
                         'Resultado esperado no especificado';
        $testCase = $this->extractFromDocblock($docComment, 'testCase') ?: 
                   sprintf('TC%03d', $counter);
        
        $doc = "## {$testCase} - `{$methodName}`\n\n";
        $doc .= "### 📋 Propósito del Test\n";
        $doc .= "{$purpose}\n\n";
        
        $doc .= "### 🎯 Resultado Esperado\n";
        $doc .= "{$expectedResult}\n\n";
        
        $doc .= "### ⚙️ Configuración del Test\n";
        $doc .= "```php\n";
        $doc .= "// Método: {$methodName}()\n";
        $doc .= "// Archivo: " . str_replace(base_path(), '', $method->getFileName()) . "\n";
        $doc .= "// Línea: " . $method->getStartLine() . "\n";
        $doc .= "```\n\n";
        
        $doc .= "### 📊 Resultado de Ejecución\n";
        $doc .= "**Estado**: ⏳ Pendiente de ejecución  \n";
        $doc .= "**Tiempo de ejecución**: N/A  \n";
        $doc .= "**Fecha de última ejecución**: N/A  \n\n";
        
        $doc .= "#### Captura de Pantalla\n";
        $doc .= "![Test Result Screenshot](screenshots/{$testCase}_result.png)\n\n";
        
        $doc .= "### 📝 Observaciones\n";
        $observations = $this->extractFromDocblock($docComment, 'observations') ?: 
                       'Sin observaciones adicionales';
        $doc .= "{$observations}\n\n";
        
        $doc .= "---\n\n";
        
        return $doc;
    }
    
    /**
     * Extraer información específica del docblock
     */
    private function extractFromDocblock(?string $docComment, string $tag): ?string
    {
        if (!$docComment) {
            return null;
        }
        
        $pattern = "/@{$tag}\s+(.+?)(?=@|\*\/|$)/s";
        if (preg_match($pattern, $docComment, $matches)) {
            return trim(preg_replace('/\s*\*\s*/', ' ', $matches[1]));
        }
        
        return null;
    }
    
    /**
     * Generar propósito basado en el nombre del método
     */
    private function generatePurposeFromMethodName(string $methodName): string
    {
        // Remover prefijo 'test_' y convertir snake_case a descripción legible
        $cleanName = str_replace('test_', '', $methodName);
        $words = str_replace('_', ' ', $cleanName);
        
        return "Verificar que " . ucfirst($words);
    }
    
    /**
     * Generar resumen del archivo de test
     */
    private function generateTestSummary(string $className, int $testCount): string
    {
        $doc = "## Resumen de Ejecución\n\n";
        $doc .= "### Estadísticas\n";
        $doc .= "- **Total de tests**: {$testCount}\n";
        $doc .= "- **Estado general**: ⏳ Pendiente\n";
        $doc .= "- **Última actualización**: " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        $doc .= "### Próximas Acciones\n";
        $doc .= "- [ ] Ejecutar suite de tests\n";
        $doc .= "- [ ] Actualizar capturas de pantalla\n";
        $doc .= "- [ ] Revisar cobertura de código\n\n";
        
        $doc .= "---\n\n";
        $doc .= "**Documentación generada automáticamente con**: `php artisan test:document`  \n";
        $doc .= "**Fecha**: " . now()->format('Y-m-d H:i:s') . "  \n";
        $doc .= "**Versión de Laravel**: " . app()->version() . "  \n";
        
        return $doc;
    }
    
    /**
     * Generar índice general de documentación
     */
    private function generateIndex(string $outputDir, array $testFiles): void
    {
        $doc = "# Índice de Documentación de Tests\n\n";
        $doc .= "Documentación generada automáticamente para los tests del proyecto.\n\n";
        $doc .= "## Tests Documentados\n\n";
        
        foreach ($testFiles as $file) {
            $className = $this->getClassNameFromFile($file);
            if ($className) {
                $reflection = new ReflectionClass($className);
                $shortName = $reflection->getShortName();
                $fileName = str_replace('Test', '', $shortName);
                $fileName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $fileName));
                
                $testMethods = $this->getTestMethods($reflection);
                $testCount = count($testMethods);
                
                $doc .= "- [{$shortName}]({$fileName}.md) - {$testCount} tests\n";
            }
        }
        
        $doc .= "\n## Información del Proyecto\n\n";
        $doc .= "- **Generado el**: " . now()->format('Y-m-d H:i:s') . "\n";
        $doc .= "- **Laravel**: " . app()->version() . "\n";
        $doc .= "- **PHP**: " . phpversion() . "\n";
        $doc .= "- **Comando**: `php artisan test:document`\n\n";
        
        File::put("{$outputDir}/README.md", $doc);
    }
}
