<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Sale;
use App\Services\Sale\SaleService;
use App\Services\Stats\StatsSalesService;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Controlador para la generacion de los diversos pdf
 */
class PDFController extends Controller
{
  /**
   * vista de un pdf de una orden de compra
   * * contactado desde la ruta: preorders/pdf/show/{id}
   * * contactado desde la ruta: purchases/details/order/pdf/show/{id}
   * @param int $id id de preorden base para la generacion del pdf
   * @return \Illuminate\Http\Response
   */
  public function open_pdf_order(int $id)
  {
    $preorder = PreOrder::findOrFail($id);

    $order_data = json_decode($preorder->order, true);
    $order_anexo = json_decode($preorder->details, true);

    $pdf = Pdf::loadView('pdf.orders.order', ['order' => $order_data, 'anexo' => $order_anexo])
      ->setPaper('a4')
      ->setOption('encoding', 'UTF-8');

    // orden_compra_order_... .pdf
    $pdf_name = 'orden_compra_' . $order_data['order_code'] . '.pdf';

    // stream a una pestaña del navegador
    return $pdf->stream($pdf_name);
  }

  /**
   * descarga de un pdf de una orden de compra
   * @param int $id id de preorden base para la generacion del pdf
   * @return \Illuminate\Http\Response
   */
  public function download_pdf_order(int $id)
  {
    $preorder = PreOrder::findOrFail($id);

    $order_data = json_decode($preorder->order, true);
    $order_anexo = json_decode($preorder->details, true);

    $pdf = Pdf::loadView('pdf.orders.order', ['order' => $order_data, 'anexo' => $order_anexo])
      ->setPaper('a4')
      ->setOption('encoding', 'UTF-8');

    // orden_compra_order_... .pdf
    $pdf_name = 'orden_compra_' . $order_data['order_code'] . '.pdf';

    // stream a una pestaña del navegador
    return $pdf->download($pdf_name);
  }

  /**
   * vista de un pdf de un comprobante de venta
   * @param int $id id de venta
   * @return \Illuminate\Http\Response
   */
  public function open_pdf_sale(int $id)
  {
    $sale = Sale::findOrFail($id);
    $sale_service = new SaleService();
    $sale_data = $sale_service->generateSaleData($sale);

    $pdf = Pdf::loadView('pdf.sales.sale', ['sale_data' => $sale_data])
      ->setPaper('a4')
      ->setOption('encoding', 'UTF-8');

    // nombre
    $codigo = $sale_data['header']['id'] . str_replace([' ', ':', '-'], '', $sale_data['header']['fecha']);
    $pdf_name = 'comprobante_venta' . $codigo . '.pdf';

    // stream a una pestaña del navegador
    return $pdf->stream($pdf_name);
  }

  /**
   * vista de un pdf de presupuesto completado
   * contactado desde 'open-pdf-quotation-supplier' (modulo proveedores, para gerencia)
   * contactado desde 'open-pdf-quotation' (apartado de presupuestos para proveedor)
   * @param int $id id de presupuesto
   * @return \Illuminate\Http\Response
   */
  public function stream_pdf_quotation(int $id)
  {
    $quotation = Quotation::findOrFail($id);
    $qps = new QuotationPeriodService();
    $quotation_data = $qps->generateQuotationPDFData($quotation);

    $pdf = Pdf::loadView('pdf.quotations.quotation', ['quotation_data' => $quotation_data])
      ->setPaper('a4')
      ->setOption('encoding', 'UTF-8');

    $codigo = $quotation->quotation_code;
    $pdf_name = $codigo . '.pdf';

    // stream a una pestaña del navegador
    return $pdf->stream($pdf_name);
  }

  /**
   * vista de un pdf de estadistica de ventas
   * @param Request $request datos de filtrado
   * @return \Illuminate\Http\Response
   */
  public function stream_pdf_sale_stat(Request $request)
  {
    $start_date = $request->query('start_date') ?? ''; //"Y-m-d"
    $end_date   = $request->query('end_date') ?? '';  //"Y-m-d"
    $product    = $request->query('product') ?? '';   //"id" de producto

    // servicios para construir query y hacer consulta de ventas
    $sss = new StatsSalesService();
    $sales = $sss->searchSales($start_date, $end_date, $product);
    $processed_sales = $sss->processSalesForTable($sales);
    $sales_flatten = $sss->flattenSalesData($processed_sales);

    // otros datos
    $fecha_emision = now()->format('d-m-Y H:i');

    $start = ($start_date === '')
      ? '-'
      : Carbon::createFromFormat('Y-m-d', $start_date)->format('d-m-Y');

    $end = ($end_date === '')
      ? '-'
      : Carbon::createFromFormat('Y-m-d', $end_date)->format('d-m-Y');

    $product_name = (is_numeric($product))
      ? Product::find((int)$product)->product_name
      : 'todos';

    $parametros = [
      'desde' => $start,
      'hasta' => $end,
      'producto' => $product_name,
    ];

    $total_ventas = $sales_flatten->reduce(function ($acc, $sale) {
      return $acc + $sale['total'];
    }, 0);

    // crear pdf
    $pdf = Pdf::loadView('pdf.sales.stat-sale', [
      'fecha' => $fecha_emision,
      'parametros' => $parametros,
      'sales' => $sales_flatten,
      'total' => $total_ventas,
    ])
      ->setPaper('a4')
      ->setOption('encoding', 'UTF-8');

    $codigo = 'estadistica_ventas_' . str_replace([' ', ':', '-'], [''], $fecha_emision);
    $pdf_name = $codigo . '.pdf';

    // stream a una pestaña del navegador
    return $pdf->stream($pdf_name);
  }
}
