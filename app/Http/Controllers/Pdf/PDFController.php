<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\Sale;
use App\Services\Sale\SaleService;
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
    $pdf_name = 'orden_compra_' . $order_data['code'] . '.pdf';

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
    $pdf_name = 'orden_compra_' . $order_data['code'] . '.pdf';

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
}
