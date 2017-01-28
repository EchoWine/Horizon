<?php

namespace Work\Controller\Admin;

use CoreWine\Http\Request;
use CoreWine\Http\Controller;
use CoreWine\View\Engine;
use Dompdf\Dompdf;
use Work\Model\Invoice;
use CoreWine\Http\Router;
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


		Router::view(['invoice' => $invoice,'filename' => $filename]);

		# This is dirty
		ob_start();
		Engine::startRoot();
		include Engine::html($file);
		Engine::endRoot();
		$html = ob_get_contents();
		ob_clean();


		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);

		//$dompdf->setPaper('A4', 'landscape');

		$dompdf->render();

		header("Content-Type: application/pdf");
		header("Content-Disposition: inline; filename='{$filename}'");
		echo $dompdf->output();

		die();
	}
}

?>