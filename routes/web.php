<?php

use App\Http\Controllers\AssemblyController;
use App\Http\Controllers\ConsultAgilController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PrecatoryController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'admin'], function(){});

Route::get('/questionary/{assembleia}', [AssemblyController::class, 'questionary'])
    ->middleware('auth:admin,web')->name('questionary');

// Route::get('/ajax', function () {
//     return view('ajax');
// })->middleware('auth:admin,web');

// Route::get('/ajaxteste', [AssemblyController::class, 'ajax'])
// ->middleware('auth:admin,web');

Route::get('/report', function () {
    return view('report');
})->middleware('auth:admin,web')->name('report');


Route::get('/resume/{assembleia}', [AssemblyController::class, 'resume'])
    ->middleware('auth:admin,web')->name('resume');

Route::post('/questionstore/{assembleia}', [AssemblyController::class, 'questStore'])
    ->middleware('auth:admin,web')->name('questionary.store');

Route::get('/dashboard', [PostController::class, 'dashboard'])
    ->middleware('auth:admin,web')->name('dashboard');

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest:admin,web')->name('login');

/*Route::get('/dashboard', function () {
    if ( Auth::guard('admin')->check()) {
        $departamentos = Departamentos::where('id_setor',  Auth::guard('admin')->user()->departamento->id_setor)->with('menus')->first();
        return view('dashboard', compact('departamentos'));
    }
    return view('dashboard');
})->name('dashboard');*/

/*Route::get('/collection', [ConsultAgilController::class, 'collection'])
            ->name('collection');*/

/*Route::get('employeedit',function () {
    $_SESSION['valor'] = 0;
    return view('employeedit');
})->middleware('auth:admin')->name('employeedit');*/

Route::get('/cron', function () {
    DB::table('password_resets')->where('created_at', '<', Carbon::now()->subDays(1))->delete();
    return view('auth.login');})
    ->name('cron');


Route::get('/collection', [ConsultAgilController::class, 'collection'])
            ->middleware('auth:admin')
            ->name('collection');

Route::get('/show/{menu}', [PostController::class, 'show'])
            ->name('show')->middleware('auth:web');

Route::get('/list/{menu}', [PostController::class, 'list'])
            ->middleware('auth:admin')->name('list');

Route::get('/subview/{menu}', [PostController::class, 'subview'])
            ->name('subview');

Route::get('/consult/{acervo}',[ConsultAgilController::class, 'index'])
                ->middleware('auth:web,admin')->name('consult');

Route::get('/editconsult/{lista}/{type}', [ConsultAgilController::class, 'caedit'])
                ->middleware('auth:admin')
                ->name('edit.ca');

Route::get('/assembly', [AssemblyController::class, 'index'])
                ->middleware('auth:admin')
                ->name('assembly.index');

Route::get('/assemblyCreate', [AssemblyController::class, 'create'])
                ->middleware('auth:admin')
                ->name('assembly.create');

Route::get('/assemblyEdit/{assembleia}', [AssemblyController::class, 'edit'])
                ->middleware('auth:admin')
                ->name('assembly.edit');

Route::post('/assemblyAdd', [AssemblyController::class, 'store'])
                ->middleware('auth:admin')
                ->name('assembly.add');

Route::post('/assemblyUpdate/{assembleia}', [AssemblyController::class, 'update'])
                ->middleware('auth:admin')
                ->name('assembly.update');

Route::delete('/assemblyDestroy', [AssemblyController::class, 'destroy'])
                ->middleware('auth:admin')
                ->name('assembly.delete');

Route::get('/questions/{assembleia}', [QuestionController::class, 'list'])
                ->middleware('auth:admin')
                ->name('question.index');

Route::get('/question/{assembleia}', [QuestionController::class, 'index'])
                ->middleware('auth:admin')
                ->name('question.create');

Route::get('/active/{assembleia}/{type}', [AssemblyController::class, 'active'])
                ->middleware('auth:admin')
                ->name('active');

Route::post('/questionCreate', [QuestionController::class, 'create'])
                ->middleware('auth:admin')
                ->name('question.store');

Route::get('/questionEdit/{question}', [QuestionController::class, 'edit'])
                ->middleware('auth:admin')
                ->name('question.edit');

Route::put('/questionUpdate/{question}', [QuestionController::class, 'update'])
                ->middleware('auth:admin')
                ->name('question.update');

Route::delete('/questionDelete', [QuestionController::class, 'destroy'])
                ->middleware('auth:admin')
                ->name('question.destroy');

Route::get('/report/participants/{assembleia}', [ReportController::class, 'reportParticipants'])
                ->middleware('auth:admin')
                ->name('report.participants');

Route::get('/report/question/{assembleia}', [ReportController::class, 'reportQuestion'])
                ->middleware('auth:admin')
                ->name('report.question');

Route::get('/report/votesUf/{assembleia}', [ReportController::class, 'reportVotesPerUF'])
                ->middleware('auth:admin')
                ->name('report.votesuf');

Route::get('/consultCreate/{acervo}', [ConsultAgilController::class, 'create'])
                ->middleware('auth:web,admin')->name('consult.create');

Route::get('/show/{menu}/create', [PostController::class, 'saving'])
                ->middleware('auth:admin')->name('posts.saving');

Route::get('/list/{menu}/create', [PostController::class, 'create'])
                ->middleware('auth:admin')->name('posts.create');

Route::get('/edit/{id}', [PostController::class, 'edit'])
                ->middleware('auth:admin')->name('posts.edit');

Route::get('/cron', function () {
    DB::table('password_resets')->where('created_at', '<', Carbon::now()->subDays(1))->delete();
    return view('auth.login');})
    ->name('cron');

Route::get('/list/download', [PostController::class, 'download'])
                ->middleware('auth:admin')->name('posts.download');

Route::get('/print', function(){
    $html = "<h1>ESTAMOS EM OBRAS, AGUARDE. </h1>";
    $pdf = PDF::loadHtml($html);
    return $pdf->stream();
              })->name('posts.print');

              /*Route::get('/print/{menu}', function($menu){
                $submenu = Submenu::find($menu);
                $contador = 0;
                $titulo = Submenu::find($menu);
                $funcionarios = Funcionario::latest()->get();
                $diretorias = Diretoria::find($menu);
                $submenus = Submenu::get();
                $tipos = TiposDocumento::get();
                $documentos = Documentos::where('id_sub_menu', $menu)->orderBy('data', 'DESC')->get();
                $pdf = PDF::loadView('list',  compact( 'menu','documentos', 'titulo' ,'funcionarios', 'diretorias', 'submenus', 'tipos', 'contador'));
                return $pdf->setpaper('a4')->stream();
                          })->name('posts.print');*/

Route::put('/list/{id}', [PostController::class, 'update'])
                ->middleware('auth:admin')->name('posts.update');

Route::post('/add/{menu}', [PostController::class, 'store'])
                ->middleware('auth:admin')->name('posts.store');

Route::post('/adding/{menu}', [PostController::class, 'save'])
                ->middleware('auth:admin')->name('posts.save');

Route::post('/editing/{id}', [PostController::class, 'store'])
                ->middleware('auth:admin')->name('posts.editar');

Route::post('/precatory/{menu}', [PrecatoryController::class, 'store'])
                ->middleware('auth:admin')->name('precatory.store');

Route::get('/editpre/{id}', [PrecatoryController::class, 'editpre'])
                ->middleware('auth:admin')->name('precatory.editpre');

Route::put('/editpre/{id}', [PrecatoryController::class, 'update'])
                ->middleware('auth:admin')->name('precatory.update');

Route::any('/delete', [PostController::class, 'destroy'])
                ->middleware('auth:admin')->name('posts.destroy');

Route::any('/deletepre', [PrecatoryController::class, 'destroy'])
                ->middleware('auth:admin')->name('precatory.destroy');

Route::any('/employeedelete', [EmployeeController::class, 'destroy'])
                ->middleware('auth:admin')->name('employee.destroy');

Route::get('/employeedit/{id}',[EmployeeController::class, 'edit'])
                ->middleware('auth:admin')->middleware('auth:admin')->name('employeedit');

Route::get('/employee',[EmployeeController::class, 'index'])
            ->middleware('auth:admin')->name('employee');

Route::post('/afillied',[EmployeeController::class, 'orderAffiliated'])
            ->name('affilied.store');

Route::put('/employeeUpdate/{id}', [EmployeeController::class, 'update'])
            ->middleware('auth:admin')
            ->name('employee.update');


Route::get('/register', [EmployeeController::class, 'create'])
            ->middleware('auth:admin')
            ->name('register');

Route::post('/registerCreate', [EmployeeController::class, 'store'])
            ->middleware('auth:admin')
            ->name('register.create');

Route::get('/profile', [EmployeeController::class, 'editProfile'])
            ->middleware('auth:web')
            ->name('profile');

Route::post('/profile', [EmployeeController::class, 'storeProfile'])
            ->middleware('auth:web')
            ->name('profile.edit');

Route::delete('/profile', [EmployeeController::class, 'removeProfile'])
            ->middleware('auth:web')
            ->name('image.destroy');

Route::post('/addCollection', [ConsultAgilController::class, 'addCollection'])
            ->middleware('auth:admin')
            ->name('collection.create');

Route::post('/search/{acervo}', [ConsultAgilController::class, 'search'])
            ->middleware('auth:web')
            ->name('search');

Route::post('/createFolder/{acervo}', [ConsultAgilController::class, 'createFolder'])
            ->middleware('auth:admin')
            ->name('caFolder.create');

Route::post('/createSubFolder/{acervo}', [ConsultAgilController::class, 'createSubFolder'])
            ->middleware('auth:admin')
            ->name('caSubFolder.create');

Route::post('/editCaDocuments/{lista}/{type}', [ConsultAgilController::class, 'editDocuments'])
            ->middleware('auth:admin')
            ->name('caDocuments.edit');

Route::post('/editCaDocuments/{type}', [ConsultAgilController::class, 'editDocuments'])
            ->middleware('auth:admin')
            ->name('caDocuments.edit');

Route::delete('/deleteFolder/{acervo}', [ConsultAgilController::class, 'deleteFolder'])
            ->middleware('auth:admin')
            ->name('folder.destroy');

Route::delete('/deleteSubFolder/{acervo}', [ConsultAgilController::class, 'deleteSubFolder'])
            ->middleware('auth:admin')
            ->name('subFolder.destroy');

Route::delete('/deleteCadocuments', [ConsultAgilController::class, 'deleteCadocuments'])
            ->middleware('auth:admin')
            ->name('cadocuments.destroy');

Route::post('/add/{menu}', [PostController::class, 'store'])
            ->middleware('auth:admin')->name('posts.store');

Route::get('/termAccepted', [EmployeeController::class, 'createAcess'])
            ->middleware('auth:web')
            ->name('user.accept');

Route::post('/editacervo', [ConsultAgilController::class, 'editAcervo'])
            ->middleware('auth:admin')->name('acervo.edit');

Route::delete('/acervoremove/{acervo}', [ConsultAgilController::class, 'deleteAcervo'])
            ->middleware('auth:admin')
            ->name('acervo.destroy');

Route::get('storages/{filename}', function($filename)
{
    try {

        $decrypted = Crypt::decrypt($filename);
        $filePath = storage_path().'/app/public/'.$decrypted;
        $newPath = str_replace('\\', "/", $filePath);

        if(File::exists($newPath)){
            return response()->file($newPath);
        }else{

            Alert::error('Você está tentando acessar um arquivo inválido');
            return back();
        }
    } catch (\Throwable $th) {
        Alert::error('Arquivo inválido');
            return back();
    }


})->middleware('auth:web,admin')->name('storage.link');

Route::get('/filiado', function(){

    return view('affiliated');

})->name('filiado.new');

require __DIR__.'/auth.php';
