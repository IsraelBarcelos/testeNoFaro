@extends('layout/app', ["current" => "pessoas"])

@section('body')
<div class="card border">
	<div class="card-body">
		<h5 class="card-title">Pessoas</h5>
		<input id="busca" class="form-control-md text-center font-weight-bold col-9 d-inline-block" type="text" placeholder="Buscar por nome ou email">
		<button onClick="busca()" class="d-inline-block col-2 bg-primary font-weight-bold">Buscar</button>
		<table class="table table-ordered table-hover">
			<thead>
				<tr>
					<th>Código</th>
					<th>Nome</th>
					<th>Email</th>
					<th>DDD</th>
					<th>Telefone</th>
					<th>Ações</th>
				</tr>
			</thead>


			<tbody id="tbody">
			</tbody>


		</table>

	</div>
	<div class="card-footer text-center">
		<button class="btn btn-primary" onclick="abrirFormulario()"> Cadastrar Pessoa</button>
	</div>
</div>





<div id="form" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Formulário Pessoal</h5>
				<button type="cancel" class="close" aria-label="Close">
					<span data-dismiss="modal" aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="addpessoa" class="form-group">
				<div class="modal-body">

					<input type="hidden" id="id">

					<div class="input-group">
						<div class="input-group-prepend">
							<label id="labelnome" class="input-group-text form-control form-control-sm">Nome: </label>
						</div>
						<input class="form-control form-control-sm mb-2" id="nome" type="text" placeholder="Digite o nome do cliente">
					</div>

					<div class="input-group">
						<div class="input-group-prepend">    						

						<label id="labelemail" class="input-group-text form-control form-control-sm">@</label>
						</div>
						<input class="form-control form-control-sm mb-2" id="email" type="email" placeholder="Digite o email do cliente">
					
					</div>

					<div class="input-group-prepend">
						<div class="input-group-prepend">

							<label id="labelddd" class="input-group-text form-control form-control-sm">DDD</label>
						</div>
						<input class="col-2 form-control form-control-sm mb-2" id="ddd" type="text" placeholder="DDD">
						<div class="input-group-append">

							<label id="labeltelefone" class="input-group-text form-control form-control-sm">Telefone</label>
							<input type="text" class="col-12 form-control form-control-sm mb-2" id="telefone">
						</div>

					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Salvar</button>
					<button type="cancel" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div><!-- menu de adicao -->
</div>
@endsection

@section('javascript')
<script type="text/javascript">
		erro = false
		$(function(){ //carrega a tabela ao final do programa
			carregarPessoas()
		})

		function carregarPessoas(){
			$.get('/api/pessoas',function(dados){
				for(let i=0;i<dados.length;i++){
					montarLinha(dados[i])
				}
			})
		}

		function montarLinha(objeto){ // monta a linha para cada elemento
			
			let linha = '<tr>'+
			'<td>'+objeto.id+'</td>'+
			'<td>'+objeto.nome+'</td>'+
			'<td>'+objeto.email+'</td>'+
			'<td>'+objeto.ddd+'</td>'+
			'<td>'+objeto.telefone+'</td>'+
			'<td>'+
			
			'<button class="mx-1 my-1 btn btn-warning" onClick="editar('+objeto.id+')">Editar</button>'+
			'<button class="mx-1 my-1 btn btn-danger" onClick="deletar('+objeto.id+')">Deletar</button>'+
			'</td>'+
			'</tr>';

			$('#tbody').append(linha)
		}


		function abrirFormulario(){ // limpar formulario quando aberto
			if(erro == false){
				$('#nome').val('')
				$('#email').val('')
				$('#id').val('')
				$('#ddd').val('')
				$('#telefone').val('')
			}
			$('#form').modal('show')
		}

		$('#addpessoa').submit(function(e){ // adicionar produto quando clicado em submit
			e.preventDefault()
			if($('#id').val() == ''){
				criaPessoa()
			}else{
				editarPessoa($('#id').val())
			}
			$('#form').modal('hide')
		})

		function criaPessoa(){ // cria objeto a ser passado para o laravel
			let pessoa = {
				nome : $('#nome').val(),
				email : $('#email').val(),
				ddd : $('#ddd').val(),
				telefone : $('#telefone').val()
			}

			$.ajax({
				url: '/api/pessoas',
				type: 'POST',
				data: pessoa,
				success: function(data) {
					montarLinha(data)       
				},
				error: function(data){
					erro = true 
					Object.keys(data.responseJSON.errors).forEach(function(erros){
						console.log('#label'+erros+'')
						document.getElementById('label'+erros).className = 'input-group-text form-control form-control-sm text-danger';
					})

				}
			})		
		}


		function editar(id){ //prepara formulario para update
			$('#id').val(id)
			$.get('/api/pessoas/'+id, function(data){

				$('#nome').val(data.nome)
				$('#email').val(data.email)
				$('#ddd').val(data.ddd)
				$('#telefone').val(data.telefone)

				$('#form').modal('show')
			})
		}

		function editarPessoa(id){ //req do update

			let pessoa = {
				nome : $('#nome').val(),
				email : $('#email').val(),
				ddd : $('#ddd').val(),
				telefone: $('#telefone').val()
			}

			$.ajax({
				url: '/api/pessoas/'+id,
				type: 'PUT',
				data: pessoa,
				success: function(data) {
					for(let i=0;i<$('#tbody>tr').length;i++){
						if($('#tbody>tr')[i].cells[0].innerText == data.id){
							$('#tbody>tr')[i].cells[1].innerText = data.nome
							$('#tbody>tr')[i].cells[2].innerText = data.email
							$('#tbody>tr')[i].cells[3].innerText = data.ddd
							$('#tbody>tr')[i].cells[4].innerText = data.telefone
						}
					}       
				}
			})
		}



		function deletar(id){
			for(let i=0;i<$('#tbody>tr').length;i++){
						if($('#tbody>tr')[i].cells[0].innerText == id){
							$('#tbody>tr')[i].cells[5].innerHTML = '<button class="btn btn-danger" onClick="deleteOk('+id+')">Confirmar Exclusão</button>'
						}
				}
		}

		function deleteOk(id){
			$.ajax({
				url: '/api/pessoas/'+id,
				type: 'DELETE',
				
				success: function(data) {
					for(let i=0;i<$('#tbody>tr').length;i++){
						if($('#tbody>tr')[i].cells[0].innerText == id){
							$('#tbody>tr')[i].remove()
						}
					}
				}
			})
		}

		function busca(texto){
			$('#tbody').empty()
			
			let busca = {
				pesquisa : $('#busca').val()
			}
			$.ajax({
				url: 'api/busca',
				type: 'GET',
				data: busca,
				success: function(data){

					data.forEach(function(objeto){
						montarLinha(objeto)
						})
					}
				})
				
			}

	</script>
	@endsection