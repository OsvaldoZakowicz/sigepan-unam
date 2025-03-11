<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PDFwrapper;

class PdfService
{
  protected PDFwrapper $pdf;

  public function __construct()
  {
    // Configuración global para todos los PDF
    Pdf::setOption([
      'defaultFont' => 'DejaVu Sans',
      'isHtml5ParserEnabled' => true,
      'isRemoteEnabled' => true,
      'isFontSubsettingEnabled' => true,
      'defaultMediaType' => 'screen',
      'defaultPaperSize' => 'a4',
      'encoding' => 'UTF-8',
    ]);
  }

  /**
   * * generar PDF de orden de compra,
   * guardar PDF en local disk,
   * guardar en la BD la ruta al PDF.
   * @param PreOrder $preorder preorden de compra base
   * @param array $pdf_order_data datos obtenidos del servicio PreOrderService para la orden definitiva
   * @param array $pdf_order_anexo datos obtenidos del anexo de la preorden para la orden definitiva
   * @return void
   */
  public function generateOrderPDF($preorder, $order_data)
  {
    $pdf = Pdf::loadView(
      'pdf.orders.order',
      ['order' => $order_data, 'anexo' => $order_data['anexo']]
    )
      ->setPaper('a4');
    //ordenes/orden_compra_order_... .pdf
    $pdf_path = 'ordenes/orden_compra_' . $order_data['code'] . '.pdf';
    $pdf->save(storage_path('app/public/' . $pdf_path));

    $preorder->order_pdf = $pdf_path;
    $preorder->save();
  }
}
