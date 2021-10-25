$(document).ready(() => {
	$('#documentacao').on('click',()=> {
    //console.log('link documentação clidado')
    //$('#pagina').load('documentacao.html')
    //pode ser get ou post
    $.get('documentacao.html', data =>{
      //console.log(data)
      $('#pagina').html(data)
    })
  })

  $('#suporte').on('click',()=> {
    //console.log('link suporte clidado')
    $.get('suporte.html', data =>{
      $('#pagina').html(data)
    })
  })


  //método ajax
  $('#competencia').on('change',evento => {
    //Debug
    //console.log($(evento.target).val())
    let competencia = $(evento.target).val()
    //console.log(competencia)

    $.ajax({
      type: 'GET',
      url:'controller/app.php',
      data: `competencia=${competencia}`, //print_r do (app.php) super global ($_GET)
      dataType: 'json',
      success: dados => {
        $('#numeroVendas').html(dados.numeroVendas)
        $('#totalVendas').html(dados.totalVendas)

        $('#clienteAtivo').html(dados.clienteAtivo)
        $('#clienteInativo').html(dados.clienteInativo)

        $('#reclamacao').html(dados.reclamacao)
        $('#sugestao').html(dados.sugestao)
        $('#elogios').html(dados.elogios)
        console.log(dados.elogios)

        $('#totalDespesas').html(dados.totalDespesas)
      },/*'Requisição realizada com sucesso!'*/
      error: erro => {console.log(erro)}/*'Erro na requisição.'*/
    })
    //método, url, dados, sucesso, erro
  })


})