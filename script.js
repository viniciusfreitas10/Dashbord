$(document).ready(() => {
	$('#documentacao').on('click', () =>{
		//$('#pagina').load('documentacao.html')
		$.get('documentacao.html',data=>{
			$('#pagina').html(data)
		})
	})
	$('#suporte').on('click', () =>{
		//$('#pagina').load('suporte.html')
		$.post('suporte.html', data =>{
			$('#pagina').html(data)
		})
	})
	$('#index').on('click', () =>{
		$('#pagina').load('index.html')
	})

	$('#competencia').on('change', e => {
		let competencia = $(e.target).val()
		$.ajax({
			//método,url,dados,sucesso,falha
			type:'GET',
			url:'app.php',
			data: 'competencia='+competencia ,
			dataType: 'json',
			success:(dados)=>{
				$('#numeroVendas').html(dados.numeroVendas)
				$('#totalVendas').html(dados.totalVendas)
				$('#clientesAtivos').html(dados.clientesAtivo)
				$('#clientesInativos').html(dados.clientesInativo)
				$('#reclamacao').html(dados.totalReclamaçao)
				$('#elogio').html(dados.totalElogio)
				$('#sugestao').html(dados.totalSugestao)
				$('#despesa').html(dados.totalDespesa)
				//console.log(dados)
			},
			error:(erro)=>{
				console.log(erro)
			}
		})
	})
})