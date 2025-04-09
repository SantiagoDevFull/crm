<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\File;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::findOrFail(1);
        $file = null;

        echo $company->logo;

        if ($company->logo) {
            $file = File::where('name',$company->logo)->first();
        };

        return view('company', [
            'company'   => $company,
            'file'      => $file
        ]);
        
    }

    public function getColorsPallette()
    {
        $company = Company::findOrFail(1);

        return response()->json([
            'menu_color' => $company->menu_color,
            'text_color' => $company->text_color
        ]);
    }

    public function getLogo()
    {
        $company = Company::findOrFail(1);
        $file = null;

        if ($company->logo) {
            $file = File::findOrFail($company->logo);
        };

        return $file;
    }

    public function files(Request $request)
    {
        $path = storage_path('app/uploads/' . $request->route('fileName'));

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function validateForm()
    {
        $messages = [
            'name.required'            => 'Debe ingresar un Nombre.',
            'contact.required'         => 'Debe ingresar un Contacto.',
            'pais.required'            => 'Debe seleccionar un País.',
            'asist_type.required'      => 'Debe seleccionar el Tipo de asistencia.',
            'sufijo.required'          => 'Debe ingresar un Sufijo.'
        ];

        $rules = [
            'name'                     => 'required',
            'contact'                  => 'required',
            'pais'                     => 'required',
            'asist_type'               => 'required',
            'sufijo'                   => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function update()
    {

        $this->validateForm();

        $id = request('id');
        $name = request('name');
        $contact = request('contact');
        $pais = request('pais');
        $asist_type = request('asist_type');
        $sufijo = request('sufijo');
        $menu_color = request('menu_color');
        $text_color = request('text_color');
        $logo = request('logo');

        $element = Company::findOrFail($id);
        $element->name = $name;
        $element->contact = $contact;
        $element->pais = $pais;
        $element->asist_type = $asist_type;
        $element->sufijo = $sufijo;
        $element->menu_color = $menu_color;
        $element->text_color = $text_color;

        if ($logo == "null") {
            $element->logo = null;
        } else {
            $element->logo = $logo;
        };

        $element->save();

        $type = 3;
        $title = 'Bien';
        $msg = 'Los datos se guardaron con éxito.';
        $url = route('dashboard.company.index');


        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg,
            'url'   => $url
        ]);
    }
}
