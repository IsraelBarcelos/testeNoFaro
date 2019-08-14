<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Helpers\Constants;
use App\Pessoa;
class PessoaController extends Controller
{

    public function index()
    {
        $pessoas = Pessoa::all();
        return response()->json($pessoas);
    }

   
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'nome' => 'required|string',
            'email' => 'required|email',
            'telefone' => 'required|digits:9',
            'ddd' => 'required|digits:2'
        ]);

        $pessoa = Pessoa::create($data);

        return response()->json($pessoa);  
    }


    public function show(Pessoa $pessoa)
    {
        return response()->json($pessoa);
    }


    public function update(Request $request, Pessoa $pessoa)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'nome' => 'required|string',
            'email' => 'required|email',
            'telefone' => 'required|digits:9',
            'ddd' => 'required|digits:2'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => "Por favor preencher o nome, email, ddd e telefone corretamente.",
            ], Constants::HTTP_BAD_REQUEST);
        }

        $pessoa->update($data);

        return $this->show($pessoa);
    }


    public function destroy(Pessoa $pessoa)
    {
        $pessoa->delete();

        return response()->json([
            'message' => 'Pessoa deletada com sucesso!'
        ], Constants::HTTP_CREATED);
    }

    public function busca(Request $request)
    {
        $pessoa = Pessoa::where('email', $request->input('pesquisa'))
        ->orWhere('nome','like','%'.$request->input('pesquisa').'%')
        ->get();
        
        return response()->json($pessoa);
    }
}
