<?php

namespace Work\Controller\Admin;

use CoreWine\Http\Request;
use CoreWine\Http\Controller;
use CoreWine\View\Engine;
use Dompdf\Dompdf;
use Work\Model\Invoice;
use CoreWine\Http\Router;
use CoreWine\Http\Response\Response;
use Auth;

class InvoicePdfController extends Controller{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];


	/**
	 * Routes
	 *
	 * @param $router
	 *
	 * @return void
	 */
	public function __routes($router){
		$router -> any('/admin/work/invoices/document/copy/{id}','copy') -> as('work:admin.invoice.document-copy');
		$router -> any('/admin/work/invoices/document/original/{id}','original') -> as('work:admin.invoice.document-original');
	}

	/**
	 * Return a PDF 
	 *
	 * @param Request $request
	 * @param integer $id
	 * 
	 * @return Response
	 */
	public function original(Request $request,$id){

		return $this -> createPdf($id, 'original', 'Work/admin/document-original');
	}

	/**
	 * Return a PDF 
	 *
	 * @param Request $request
	 * @param integer $id
	 * 
	 * @return Response
	 */
	public function copy(Request $request,$id){

		return $this -> createPdf($id,'copy', 'Work/admin/document-copy');
		
	}

	public function createPdf($id,$type,$file){
		$invoice = Invoice::where('id',$id) -> where('user_id',Auth::user() -> id) -> first();

		if(!$invoice)
			abort(404);

		$filename = 'Fattura '.$invoice -> getIdentification().".pdf";


		$html = view($file,['invoice' => $invoice,'filename' => $filename]);

		$dompdf = new Dompdf();
		$dompdf -> loadHtml($html);
		$dompdf -> render();

		$response = new Response();
		$response -> header('Content-Type','application/pdf');
		$response -> header('Content-Disposition',"inline; filename='{$filename}'");
		$response -> setBody($dompdf -> output());

		return $response;
	}
}

?>