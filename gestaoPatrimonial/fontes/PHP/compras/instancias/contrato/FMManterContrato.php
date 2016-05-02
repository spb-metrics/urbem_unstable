<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Formulário para inserção de contratos para compra direta
    * Data de Criação   :  01/10/2008

    * @author Analista: Gelson W.
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * Casos de uso:

    $Id : $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_LIC_COMPONENTES."ISelectDocumento.class.php");
include_once( CAM_GP_COM_COMPONENTES."IMontaNumeroCompraDireta.class.php" );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");
require_once( CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeUsuario.class.php" );
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
include_once( CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php");
include_once (TLIC."TLicitacaoTipoContrato.class.php");
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContratoDocumento.class.php");
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoContrato.class.php");
include_once( TCOM."TComprasContratoCompraDireta.class.php" );
include_once( TCOM."TComprasFornecedor.class.php");
include_once (TLIC."TLicitacaoContratoArquivo.class.php");
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"  );
include_once ( CAM_GP_COM_MAPEAMENTO . "TComprasTipoObjeto.class.php" );
include_once ( TLIC.'TLicitacaoTipoInstrumento.class.php' );
include_once ( TLIC.'TLicitacaoTipoGarantia.class.php' );

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);
Sessao::remove('arValores');
Sessao::remove('arDocumentos');
Sessao::remove('arArquivos');
SistemaLegado::mostraVar($_REQUEST);
$stAcao = $request->get('stAcao');
$inNumContrato = $request->get('inNumContrato');
$inCodEntidade = $request->get('inCodEntidade');
$stExercicio = $request->get('stExercicio');

$obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
$obTLicitacaoTipoContrato->recuperaTodos( $rsTipoContrato, ' WHERE ativo IS TRUE ', ' ORDER BY descricao ' );

$obTLicitacaoTipoInstrumento = new TLicitacaoTipoInstrumento();
$obTLicitacaoTipoInstrumento->recuperaTodos( $rsTipoInstrumento );

$obTLicitacaoTipoGarantia = new TLicitacaoTipoGarantia();
$obTLicitacaoTipoGarantia->recuperaTodos( $rsTipoGarantia );

if ($inNumContrato) {
  $obTContratoCompraDireta = new TComprasContratoCompraDireta();

  $stFiltro  =" AND contrato.num_contrato =".$inNumContrato;
  $stFiltro .=" AND contrato.cod_entidade =".$inCodEntidade;
  $stFiltro .=" AND contrato.exercicio = '".$stExercicio."'";

  $obTContratoCompraDireta->recuperaContratosCompraDireta($rsContrato, $stFiltro);$obTContratoCompraDireta->debug();

  $inCodCompraDireta = $rsContrato->getCampo('cod_compra_direta');
  $inCodModalidade = $rsContrato->getCampo('cod_modalidade');
  $stDescObjeto = $rsContrato->getCampo('descricao');
  $inCGMResponsavelJuridico = $rsContrato->getCampo('cgm_responsavel_juridico');
  $stDataAssinatura = $rsContrato->getCampo('dt_assinatura');
  $stDataVencimento = $rsContrato->getCampo('vencimento');
  $dtFimExecucao    = $rsContrato->getCampo('fim_execucao');
  $dtInicioExecucao = $rsContrato->getCampo('inicio_execucao');
  $inCGMContratado = $rsContrato->getCampo('cgm_contratado');
  $stNomContratado = $rsContrato->getCampo('nom_contratado');
  $stNomCGM = $rsContrato->getCampo('responsavel_juridico');
  $inCodDocumento = $rsContrato->getCampo('cod_documento');
  $inCodTipoDocumento = $rsContrato->getCampo('cod_tipo_documento');
  $nmValorGarantiaExecucao = number_format($rsContrato->getCampo('valor_garantia'),2,',','.');
  $vlContrato = number_format($rsContrato->getCampo('valor_contratado'),2,',','.');
  $stTipoContrato = $rsContrato->getCampo('tipo_descricao');
  $inExercicioContrato  = $rsContrato->getCampo('exercicio_contrato');
  $inExercicioCompra  = $rsContrato->getCampo('exercicio_compra_direta');
  $stTipoInstrumento = $rsContrato->getCampo('cod_tipo_instrumento');
  
  $stNomEntidade = $rsContrato->getCampo('nom_entidade');
  $inNumOrgao = $rsContrato->getCampo('num_orgao');
  $stNomOrgao = $rsContrato->getCampo('nom_orgao');
  $inNumUnidade = $rsContrato->getCampo('num_unidade');
  $stNomUnidade = $rsContrato->getCampo('nom_unidade');
  $inNumeroContrato = $rsContrato->getCampo('numero_contrato');
  $inCodTipoObjeto = $rsContrato->getCampo('cod_tipo_objeto');
  $stTipoObjeto = $rsContrato->getCampo('tipo_objeto');
  $stObjeto = $rsContrato->getCampo('objeto');
  $stFormaFornecimento = $rsContrato->getCampo('forma_fornecimento');
  $stFormaPagamento = $rsContrato->getCampo('forma_pagamento');
  $inCGMSignatario = $rsContrato->getCampo('cgm_signatario');
  $stCGMSignatario = $rsContrato->getCampo('nom_signatario');
  $stPrazoExecucao = $rsContrato->getCampo('prazo_execucao');
  $stMultaRescisoria = $rsContrato->getCampo('multa_rescisoria');
  $stJustificativa = $rsContrato->getCampo('justificativa');
  $stRazao = $rsContrato->getCampo('razao');
  $stFundamentacaoLegal = $rsContrato->getCampo('fundamentacao_legal');
  $stMultaInadimplemento = $rsContrato->getCampo('multa_inadimplemento');
  $inCGMRepresentanteLegal = $rsContrato->getCampo('cgm_representante_legal');
  if($inCGMRepresentanteLegal){
    $stNomRepresentanteLegal = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm" , " WHERE numcgm = ".$inCGMRepresentanteLegal);
  }else {
    $stNomRepresentanteLegal = '';
  }
  $inCodGarantia = $rsContrato->getCampo('cod_garantia');

  $obTContratoDocumento = new TLicitacaoContratoDocumento;
  $obTContratoDocumento->setDado('num_contrato', $inNumContrato);
  $obTContratoDocumento->setDado('cod_entidade', $inCodEntidade);
  $obTContratoDocumento->setDado('exercicio', $stExercicio );
  $obTContratoDocumento->recuperaDocumentos($rsDocumentos);
  $arDocumentos = array();
  $inCount = 0;

  while (!$rsDocumentos->eof()) {
     $arDados = array();
     $arDados['boNovo'] = false;
     $arDados['id'            ] = $inCount + 1;
     $arDados['inCodDocumento'] = $rsDocumentos->getCampo('cod_documento');
     $arDados['dtValidade'] = $rsDocumentos->getCampo('dt_validade');
     $arDados['dtEmissao'] = $rsDocumentos->getCampo('dt_emissao');
     $arDados['stNumDocumento'] = $rsDocumentos->getCampo('num_documento');
     $arDados['stNomDocumento'] = $rsDocumentos->getCampo('nom_documento');
     $arDocumentos[] = $arDados;
     $rsDocumentos->proximo();
     $inCount++;
  }
  Sessao::write('arDocumentos', $arDocumentos);

  //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
  $obTLicitacaoPublicacaoContrato = new TLicitacaoPublicacaoContrato();
  $obTLicitacaoPublicacaoContrato->setDado('num_contrato', $inNumContrato );
  $obTLicitacaoPublicacaoContrato->setDado('exercicio', $stExercicio );
  $obTLicitacaoPublicacaoContrato->setDado('cod_entidade', $inCodEntidade );
  $obTLicitacaoPublicacaoContrato->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );
  $inCount = 0;
  $arValores = array();
  while ( !$rsVeiculosPublicacao->eof() ) {
      $arValores[$inCount]['id'            ] = $inCount + 1;
      $arValores[$inCount]['inVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
      $arValores[$inCount]['stVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'nom_veiculo');
      $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao');
      $arValores[$inCount]['inNumPublicacao']  = $rsVeiculosPublicacao->getCampo( 'num_publicacao');
      $arValores[$inCount]['stObservacao'  ] = $rsVeiculosPublicacao->getCampo( 'observacao');
      $inCount++;
      $rsVeiculosPublicacao->proximo();
  }
  Sessao::write('arValores', $arValores);

  //recupera os arquivos digitais
  $stFiltro = " WHERE num_contrato = ".$inNumContrato." and cod_entidade = ".$inCodEntidade." and exercicio = '".$inExercicioContrato."' ";
  $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
  $obTLicitacaoContratoArquivo->recuperaTodos($rsContratoArquivo, $stFiltro);
  $inCount = 0;
  $arArquivos = array();
  while ( !$rsContratoArquivo->eof() ) {
      $arArquivos[$inCount]['id'       ]   = $inCount + 1;
      $arArquivos[$inCount]['arquivo']     = $rsContratoArquivo->getCampo( 'arquivo' );
      $arArquivos[$inCount]['nom_arquivo'] = $rsContratoArquivo->getCampo( 'nom_arquivo' );
      $arArquivos[$inCount]['num_contrato'] = $rsContratoArquivo->getCampo( 'num_contrato' );
      $arArquivos[$inCount]['cod_entidade'] = $rsContratoArquivo->getCampo( 'cod_entidade' );
      $arArquivos[$inCount]['exercicio'] = $rsContratoArquivo->getCampo( 'exercicio' );
      $inCount++;
      $rsContratoArquivo->proximo();
  }
  Sessao::write('arArquivos', $arArquivos);
}

$stAcao = $stAcao ? $stAcao : 'incluirCD';
$stDescObjeto = (isset($stDescObjeto)) ? $stDescObjeto : '';
$stDataAssinatura = (isset($stDataAssinatura )) ? $stDataAssinatura : '';
$stDataVencimento = (isset($stDataVencimento )) ? $stDataVencimento : '';
$stNomCGM = (isset($stNomCGM)) ? $stNomCGM : '';
$vlContrato = (isset($vlContrato)) ? $vlContrato : '';
$nmValorGarantiaExecucao = (isset($nmValorGarantiaExecucao )) ? $nmValorGarantiaExecucao : '';
$inCGMResponsavelJuridico = (isset($inCGMResponsavelJuridico )) ? $inCGMResponsavelJuridico : '';
$inNumDiasValido = (isset($inNumDiasValido )) ? $inNumDiasValido : '';
$dtInicioExecucao = (isset($dtInicioExecucao )) ? $dtInicioExecucao : '';
$dtFimExecucao = (isset($dtFimExecucao )) ? $dtFimExecucao : '';
$dt_emissao = ($request->get('dt_emissao') == '' ) ? '' : $request->get('dt_emissao');

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );
$obForm->setEncType( "multipart/form-data" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setId    ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""              );

$obHdnDescObjeto = new Hidden;
$obHdnDescObjeto->setId     ( 'hdnDescObjeto');
$obHdnDescObjeto->setName   ( 'hdnDescObjeto');
$obHdnDescObjeto->setValue  ( $stDescObjeto  );

$obHdnValorContrato = new Hidden;
$obHdnValorContrato->setName('hdnValorContrato');
$obHdnValorContrato->setId  ('hdnValorContrato');
$obHdnValorContrato->setValue($vlContrato );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName('HdnCodDocumento');
$obHdnCodDocumento->setId  ('HdnCodDocumento');

//Carrega Orgãos
$obTOrcamentoOrgao = new TOrcamentoOrgao;
$obTOrcamentoOrgao->recuperaRelacionamento( $rsOrgao, " AND OO.exercicio = '".Sessao::getExercicio()."' ");

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo( "Órgão" );
$obTxtOrgao->setTitle( "Selecione o orgão orçamentário." );
$obTxtOrgao->setName( "inNumOrgaoTxt" );
$obTxtOrgao->setId( "inNumOrgaoTxt" );
$obTxtOrgao->setSize( 10 );
$obTxtOrgao->setMaxLength( 10 );
$obTxtOrgao->setInteiro( true );
$obTxtOrgao->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumOrgao='+this.value, 'MontaUnidade');" );
$obTxtOrgao->setValue( $inNumOrgao );
$obTxtOrgao->setNull( false );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo( "Órgão" );
$obCmbOrgao->setName( "inNumOrgao" );
$obCmbOrgao->setId( "inNumOrgao" );
$obCmbOrgao->setCampoID( "num_orgao" );
$obCmbOrgao->setCampoDesc( "nom_orgao" );
$obCmbOrgao->addOption( "", "Selecione" );
$obCmbOrgao->preencheCombo( $rsOrgao );
$obCmbOrgao->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumOrgao='+this.value, 'MontaUnidade');" );
$obCmbOrgao->setValue( $inNumOrgao );
$obCmbOrgao->setNull( false );
$obCmbOrgao->setStyle( "width:300px;" );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo( "Unidade" );
$obTxtUnidade->setTitle( "Selecione a unidade orçamentária." );
$obTxtUnidade->setName( "inNumUnidadeTxt" );
$obTxtUnidade->setId( "inNumUnidadeTxt" );
$obTxtUnidade->setValue( $inNumUnidade );
$obTxtUnidade->setSize( 10 );
$obTxtUnidade->setMaxLength( 10 );
$obTxtUnidade->setInteiro( true );
$obTxtUnidade->setNull( false );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo( "Unidade" );
$obCmbUnidade->setName( "inNumUnidade" );
$obCmbUnidade->setId( "inNumUnidade" );
$obCmbUnidade->setCampoID( "num_unidade" );
$obCmbUnidade->setCampoDesc( "descricao" );
$obCmbUnidade->addOption( "", "Selecione" );
$obCmbUnidade->setValue( $inNumUnidade );
$obCmbUnidade->setNull( false );
$obCmbUnidade->setStyle( "width:300px;" );

//Carrega Tipos de Objetos
$obTTipoObjeto = new TComprasTipoObjeto;
$obTTipoObjeto->recuperaTodos( $rsTipoObjeto );

$obCmbTipoObjeto = new Select();
$obCmbTipoObjeto->setName     ( 'inCodTipoObjeto' );
$obCmbTipoObjeto->setRotulo   ( 'Tipo de Objeto' );
$obCmbTipoObjeto->setTitle    ( 'Selecione o Tipo de Objeto.' );
$obCmbTipoObjeto->setId       ( 'inCodTipoObjeto' );
$obCmbTipoObjeto->setCampoID  ( 'cod_tipo_objeto' );
$obCmbTipoObjeto->setCampoDesc( 'descricao'  );
$obCmbTipoObjeto->addOption   ( '','Selecione' );
$obCmbTipoObjeto->preencheCombo( $rsTipoObjeto );
$obCmbTipoObjeto->setValue    ( $inCodTipoObjeto );

$obTxtNumeroContrato = new TextBox;
$obTxtNumeroContrato->setName  ( "inNumeroContrato" );
$obTxtNumeroContrato->setId  ( "inNumeroContrato" );
$obTxtNumeroContrato->setRotulo( "Número do Contrato" );
$obTxtNumeroContrato->setTitle ( "Informe o número do contrato." );
$obTxtNumeroContrato->setSize ( 10 );
$obTxtNumeroContrato->setInteiro(true);
$obTxtNumeroContrato->setMaxLength( 10 );
$obTxtNumeroContrato->setNull( false );
$obTxtNumeroContrato->setValue($inNumeroContrato);

if ($stAcao == 'alterarCD') {
    $obLblTipoContrato = new Label;
    $obLblTipoContrato->setRotulo ( "Tipo de Contrato");
    $obLblTipoContrato->setValue ( $stTipoContrato );
    
    $obLblExercicioContrato = new Label;
    $obLblExercicioContrato->setRotulo ( "Exercício do Contrato");
    $obLblExercicioContrato->setValue ( $inExercicioContrato );

    $obLblExercicioLicitacao = new Label;
    $obLblExercicioLicitacao->setRotulo ( "Exercício da Compra");
    $obLblExercicioLicitacao->setValue ( $inExercicioCompra );

    $obLblEntidade = new Label;
    $obLblEntidade->setRotulo ( "Entidade");
    $obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );
    
    $obHdnNumeroContrato = new Hidden;
    $obHdnNumeroContrato->setName ( 'inHdnNumeroContrato' );
    $obHdnNumeroContrato->setValue ( $inNumeroContrato );
    
    $obLblTipoObjeto = new Label;
    $obLblTipoObjeto->setRotulo ( "Tipo de Objeto");
    $obLblTipoObjeto->setValue ( $inCodTipoObjeto.' - '.$stTipoObjeto );
    $obHdnTipoObjeto = new Hidden;
    $obHdnTipoObjeto->setName ( 'inCodTipoObjeto' );
    $obHdnTipoObjeto->setValue ( $inCodTipoObjeto );
    
    $obHdnNomRepresentante = new Hidden;
    $obHdnNomRepresentante->setName('stNomCGM');
    $obHdnNomRepresentante->setId  ('stNomCGM');
    $obHdnNomRepresentante->setValue($stNomCGM );
    
    # Campo Chave
    $obHdnNumContrato = new Hidden;
    $obHdnNumContrato->setName ( 'inNumContrato' );
    $obHdnNumContrato->setValue ( $inNumContrato );
    
    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName('stExercicio');
    $obHdnExercicio->setValue($stExercicio);
    
    $obLblNumeroCompraDireta= new Label;
    $obLblNumeroCompraDireta->setRotulo    ( "Número da Compra Direta" );
    $obLblNumeroCompraDireta->setValue     ( $inCodCompraDireta);
    $obHdnNumeroCompraDireta = new Hidden; 
    $obHdnNumeroCompraDireta->setName      ( 'inCodCompraDireta');
    $obHdnNumeroCompraDireta->setValue     ( $inCodCompraDireta);
    $obHdnCodEntidade = new Hidden;        
    $obHdnCodEntidade->setName             ( 'inCodEntidade' );
    $obHdnCodEntidade->setValue            ( $inCodEntidade );
    $obHdnCodModalidade = new Hidden;      
    $obHdnCodModalidade->setName           ( 'inCodModalidade');
    $obHdnCodModalidade->setValue          ( $inCodModalidade );

    $obLblResponsavelJuridico = new Label;
    $obLblResponsavelJuridico->setRotulo('Responsável Jurídico');
    $obLblResponsavelJuridico->setValue($inCGMResponsavelJuridico.'-'.$stNomCGM);
    $obHdnResponsavelJuridico = new Hidden;
    $obHdnResponsavelJuridico->setName('inCGM');
    $obHdnResponsavelJuridico->setValue($inCGMResponsavelJuridico);

    $obLblContratado = new Label;
    $obLblContratado->setRotulo('Contratado');
    $obLblContratado->setValue($inCGMContratado.'-'.$stNomContratado);
    $obHdnContratado = new Hidden;
    $obHdnContratado->setName('inCGMContratado');
    $obHdnContratado->setValue($inCGMContratado);
    $obHdnNomContratado = new Hidden;
    $obHdnNomContratado->setName('stNomContratado');
    $obHdnNomContratado->setValue($stNomContratado);
}

if ($stAcao == 'incluirCD') {
  $obMontaCompraDireta = new IMontaNumeroCompraDireta($obForm);
  $obMontaCompraDireta->obITextBoxSelectEntidadeGeral->setNull(false);
  $obMontaCompraDireta->setSelecionaAutomaticamenteCompraDireta(true);
  $obMontaCompraDireta->obISelectModalidade->obEvento->setOnBlur($obMontaCompraDireta->obISelectModalidade->obEvento->getOnBlur()."montaParametrosGET('preencheObjetoCompraDireta', 'stExercicioCompraDireta,inCodEntidade,inCodModalidade,inCodCompraDireta', false );" );
  
  $obMontaCompraDireta->obISelectModalidade->arOption = array();
  $obMontaCompraDireta->obISelectModalidade->addOption("","Selecione"                  );
  $obMontaCompraDireta->obISelectModalidade->addOption("8","8 - Dispensa de Licitação" );
  $obMontaCompraDireta->obISelectModalidade->addOption("9","9 - Inexibilidade"         );
  $obMontaCompraDireta->obCmbCompraDireta->obEvento->setOnBlur("montaParametrosGET('preencheObjetoCompraDireta', 'stExercicioCompraDireta,inCodEntidade,inCodModalidade,inCodCompraDireta', false );");
  $obMontaCompraDireta->obExercicio->setRotulo('Exercicio da Compra');
  
  $obIPopUpCGM = new IPopUpCGM($obForm);
  $obIPopUpCGM->setRotulo('Responsável Jurídico');
  $obIPopUpCGM->setTitle('Informe o Responsavel Jurídico.');
  $obIPopUpCGM->setValue($stNomCGM);
  $obIPopUpCGM->obCampoCod->setValue($inCGMResponsavelJuridico);

  $obCmbContratado = new Select;
  $obCmbContratado->setRotulo('Contratado');
  $obCmbContratado->setName('inCGMContratado');
  $obCmbContratado->setId('inCGMContratado');
  $obCmbContratado->setNull(false);
  $obCmbContratado->setTitle('Selecione o fornecedor contratado.');
  $obCmbContratado->addOption('', 'Selecione');
  $obCmbContratado->setCampoId('cgm_fornecedor');
  $obCmbContratado->setCampoDesc('nom_cgm');
  $obCmbContratado->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCGMFornecedor='+this.value+'&inCodEntidade='+document.frm.inCodEntidade.value+'&inCodModalidade='+document.frm.inCodModalidade.value+'&inCodCompraDireta='+document.frm.inCodCompraDireta.value+'&exercicio='+document.frm.stExercicioCompraDireta.value,'carregaValorFornecedorCompraDireta');");

  
}
$obCGMRepresentanteLegal = new IPopUpCGMVinculado( $obForm );
$obCGMRepresentanteLegal->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
$obCGMRepresentanteLegal->setCampoVinculo     ( 'numcgm' );
$obCGMRepresentanteLegal->setNomeVinculo      ( 'CGM Representante Legal' );
$obCGMRepresentanteLegal->setRotulo           ( 'CGM Representante Legal' );
$obCGMRepresentanteLegal->setTitle            ( 'Informe o CGM de quem será Representante Legal do Contratado.');
$obCGMRepresentanteLegal->setName             ( 'stCGMRepresentanteLegal');
$obCGMRepresentanteLegal->setId               ( 'stCGMRepresentanteLegal');
$obCGMRepresentanteLegal->obCampoCod->setName ( 'inCGMRepresentanteLegal' );
$obCGMRepresentanteLegal->obCampoCod->setId   ( 'inCGMRepresentanteLegal' );
$obCGMRepresentanteLegal->obCampoCod->setValue( $inCGMRepresentanteLegal );
$obCGMRepresentanteLegal->obCampoCod->setNull ( false );
$obCGMRepresentanteLegal->setNull             ( false );
$obCGMRepresentanteLegal->setValue( $stNomRepresentanteLegal );
  
$obCmbTipoContrato = new Select();
$obCmbTipoContrato->setRotulo( 'Tipo de contrato' );
$obCmbTipoContrato->setTitle( 'Selecione o tipo de contrato' );
$obCmbTipoContrato->setName( 'inTipoContrato' );
$obCmbTipoContrato->setId( 'inTipoContrato' );
$obCmbTipoContrato->addOption( '', 'Selecione' );
$obCmbTipoContrato->setCampoId( 'cod_tipo' );
$obCmbTipoContrato->setCampoDesc( 'descricao' );
$obCmbTipoContrato->setStyle('width: 300');
$obCmbTipoContrato->setNull(false);
$obCmbTipoContrato->preencheCombo( $rsTipoContrato );

$obCmbTipoInstrumento = new Select();
$obCmbTipoInstrumento->setRotulo( 'Tipo de Instrumento' );
$obCmbTipoInstrumento->setTitle( 'Selecione o tipo de instrumento' );
$obCmbTipoInstrumento->setName( 'inTipoInstrumento' );
$obCmbTipoInstrumento->setId( 'inTipoInstrumento' );
$obCmbTipoInstrumento->addOption( '', 'Selecione' );
$obCmbTipoInstrumento->setCampoId( 'cod_tipo' );
$obCmbTipoInstrumento->setCampoDesc( 'descricao' );
$obCmbTipoInstrumento->setStyle('width: 300');
$obCmbTipoInstrumento->setNull(false);
$obCmbTipoInstrumento->preencheCombo( $rsTipoInstrumento );
$obCmbTipoInstrumento->setValue( $stTipoInstrumento );

$obTxtObjeto = new TextArea;
$obTxtObjeto->setId     ( "stObjeto" );
$obTxtObjeto->setName   ( "stObjeto" );
$obTxtObjeto->setRotulo ( "Objeto do Contrato" );
$obTxtObjeto->setTitle  ( "Informe todos detalhes do contrato.");
$obTxtObjeto->setNull( false );
$obTxtObjeto->setRows   ( 2 );
$obTxtObjeto->setCols   ( 100 );
$obTxtObjeto->setMaxCaracteres( 100 );
$obTxtObjeto->setValue( $stObjeto );

$obTxtFormaFornecimento = new TextArea;
$obTxtFormaFornecimento->setId     ( "stFormaFornecimento" );
$obTxtFormaFornecimento->setName   ( "stFormaFornecimento" );
$obTxtFormaFornecimento->setValue  ( "" );
$obTxtFormaFornecimento->setRotulo ( "Forma de Fornecimento" );
$obTxtFormaFornecimento->setTitle  ( "Descrição da forma de fornecimento ou regime de execução, conforme previsão do art. 55, II, da Lei Federal n. 8.666/93.");
$obTxtFormaFornecimento->setNull( false );
$obTxtFormaFornecimento->setRows ( 2 );
$obTxtFormaFornecimento->setCols ( 70 );
$obTxtFormaFornecimento->setMaxCaracteres ( 100 );
$obTxtFormaFornecimento->setValue( $stFormaFornecimento );

$obTxtFormaPagamento = new TextArea;
$obTxtFormaPagamento->setId     ( "stFormaPagamento" );
$obTxtFormaPagamento->setName   ( "stFormaPagamento" );
$obTxtFormaPagamento->setValue  ( "" );
$obTxtFormaPagamento->setRotulo ( "Forma de Pagamento" );
$obTxtFormaPagamento->setTitle  ( "Descrever o preço e as condições de pagamento, os critérios, data-base e periodicidade do reajustamento de preços, os critérios de atualização monetária entre a data do adimplemento das obrigações e a do efetivo pagamento, conforme previsão do art. 55, III, da Lei Federal n. 8.666/93" );
$obTxtFormaPagamento->setNull( false );
$obTxtFormaPagamento->setRows ( 2 );
$obTxtFormaPagamento->setCols ( 70 );
$obTxtFormaPagamento->setMaxCaracteres ( 100 );
$obTxtFormaPagamento->setValue( $stFormaPagamento );

$obCGMSignatario = new IPopUpCGMVinculado( $obForm );
$obCGMSignatario->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
$obCGMSignatario->setCampoVinculo     ( 'numcgm' );
$obCGMSignatario->setNomeVinculo      ( 'CGM Signatário' );
$obCGMSignatario->setRotulo           ( 'CGM Signatário' );
$obCGMSignatario->setTitle            ('Informe o CGM de quem será Signatário da Entidade.');
$obCGMSignatario->setName             ( 'stCGMSignatario');
$obCGMSignatario->setId               ( 'stCGMSignatario');
$obCGMSignatario->obCampoCod->setName ( 'inCGMSignatario' );
$obCGMSignatario->obCampoCod->setId   ( 'inCGMSignatario' );
$obCGMSignatario->obCampoCod->setValue( $inCGMSignatario );
$obCGMSignatario->obCampoCod->setNull ( false );
$obCGMSignatario->setNull             ( false );
$obCGMSignatario->setValue( $stCGMSignatario );

$obTxtPrazoExecucao = new TextArea;
$obTxtPrazoExecucao->setId     ( "stPrazoExecucao" );
$obTxtPrazoExecucao->setName   ( "stPrazoExecucao" );
$obTxtPrazoExecucao->setValue  ( "" );
$obTxtPrazoExecucao->setRotulo ( "Prazo de Execução" );
$obTxtPrazoExecucao->setTitle  ( "Os prazos de início de etapas de execução, de conclusão, de entrega, de observação e de recebimento definitivo, conforme o caso, de acordo com a previsão do art. 55, IV, da Lei Federal n. 8.666/93." );
$obTxtPrazoExecucao->setNull( false );
$obTxtPrazoExecucao->setRows ( 2 );
$obTxtPrazoExecucao->setCols ( 70 );
$obTxtPrazoExecucao->setMaxCaracteres ( 100 );
$obTxtPrazoExecucao->setValue( $stPrazoExecucao );

$obTxtMultaInadimplemento = new TextArea;
$obTxtMultaInadimplemento->setId     ( "stMultaInadimplemento" );
$obTxtMultaInadimplemento->setName   ( "stMultaInadimplemento" );
$obTxtMultaInadimplemento->setValue  ( "" );
$obTxtMultaInadimplemento->setRotulo ( "Multa Inadimplemento" );
$obTxtMultaInadimplemento->setTitle  ( "Descrição da previsão de multa inadimplemento, conforme previsão do art. 55, VII, da Lei Federal n. 8.666/93." );
$obTxtMultaInadimplemento->setNull ( false );
$obTxtMultaInadimplemento->setRows ( 2 );
$obTxtMultaInadimplemento->setCols ( 70 );
$obTxtMultaInadimplemento->setMaxCaracteres ( 100 );
$obTxtMultaInadimplemento->setValue( $stMultaInadimplemento );

$obTxtMultaRescisoria = new TextArea;
$obTxtMultaRescisoria->setId     ( "stMultaRescisoria" );
$obTxtMultaRescisoria->setName   ( "stMultaRescisoria" );
$obTxtMultaRescisoria->setValue  ( "" );
$obTxtMultaRescisoria->setRotulo ( "Multa Rescisória" );
$obTxtMultaRescisoria->setTitle  ( "Dica para o campo (hint): Descrição da previsão de multa rescisória, conforme previsão do art. 55, VII, da Lei Federal n. 8.666/93." );
$obTxtMultaRescisoria->setNull ( false );
$obTxtMultaRescisoria->setRows ( 2 );
$obTxtMultaRescisoria->setCols ( 70 );
$obTxtMultaRescisoria->setMaxCaracteres ( 100 );
$obTxtMultaRescisoria->setValue( $stMultaRescisoria );

$obTxtExercicioContrato = new TextBox;
$obTxtExercicioContrato->setName  ( "inExercicioContrato" );
$obTxtExercicioContrato->setId  ( "inExercicioContrato" );
$obTxtExercicioContrato->setRotulo( "Exercício da Contrato" );
$obTxtExercicioContrato->setMaxLength(4);
$obTxtExercicioContrato->setSize(4);
$obTxtExercicioContrato->setInteiro(true);
$obTxtExercicioContrato->setNull( false );
$obTxtExercicioContrato->setValue(Sessao::getExercicio());
$obTxtExercicioContrato->setReadOnly(true);

$obLblDescObjeto = new Label;
$obLblDescObjeto->setRotulo ( "Objeto" );
$obLblDescObjeto->setId     ( 'stDescObjeto');
$obLblDescObjeto->setValue  ( $stDescObjeto );

$obTxtValorContrato = new Moeda;
$obTxtValorContrato->setName  ( "vlContrato" );
$obTxtValorContrato->setId  ( "vlContrato" );
$obTxtValorContrato->setRotulo( "Valor do Contrato" );
$obTxtValorContrato->setNull( false );
if ($vlContrato == '') {
    $vlContrato = '0,00';
}
$obTxtValorContrato->setValue( $vlContrato );

$obTxtDataAssinatura = new Data;
$obTxtDataAssinatura->setName('dtAssinatura');
$obTxtDataAssinatura->setValue($stDataAssinatura);
$obTxtDataAssinatura->setNull(false);
$obTxtDataAssinatura->setRotulo('Data da Assinatura');
$obTxtDataAssinatura->setTitle('Informe a data da assinatura.');

$obTxtVencimento = new Data;
$obTxtVencimento->setName('dtVencimento');
$obTxtVencimento->setValue($stDataVencimento);
$obTxtVencimento->setNull(false);
$obTxtVencimento->setRotulo('Vencimento');
$obTxtVencimento->setTitle('Informe o vencimento do contrato.');

$obTxtDataInicioExecucao = new Data;
$obTxtDataInicioExecucao->setName   ( 'dtInicioExecucao'                     );
$obTxtDataInicioExecucao->setId     ( 'dtInicioExecucao'                     );
$obTxtDataInicioExecucao->setValue  ( $dtInicioExecucao                      );
$obTxtDataInicioExecucao->setRotulo ( 'Data de Início de Execução'           );
$obTxtDataInicioExecucao->setTitle  ( 'Informe a data de início de execução.' );
$obTxtDataInicioExecucao->setNull   ( false                                  );

$obTxtDataFimExecucao = new Data;
$obTxtDataFimExecucao->setName   ( 'dtFimExecucao'                     );
$obTxtDataFimExecucao->setId     ( 'dtFimExecucao'                     );
$obTxtDataFimExecucao->setValue  ( $dtFimExecucao                      );
$obTxtDataFimExecucao->setRotulo ( 'Data de Fim de Execução'           );
$obTxtDataFimExecucao->setTitle  ( 'Informe a data de fim de execução.' );
$obTxtDataFimExecucao->setnull   ( false                               );

$obCmbTipoGarantia = new Select();
$obCmbTipoGarantia->setRotulo( 'Tipo de Garantia' );
$obCmbTipoGarantia->setTitle( 'Selecione o tipo de garantia' );
$obCmbTipoGarantia->setName( 'inTipoGarantia' );
$obCmbTipoGarantia->setId( 'inTipoGarantia' );
$obCmbTipoGarantia->addOption( '', 'Selecione' );
$obCmbTipoGarantia->setCampoId( 'cod_garantia' );
$obCmbTipoGarantia->setCampoDesc( 'descricao' );
$obCmbTipoGarantia->setStyle('width: 300');
$obCmbTipoGarantia->setNull(false);
$obCmbTipoGarantia->preencheCombo( $rsTipoGarantia );
$obCmbTipoGarantia->setValue($inCodGarantia);

$obTxtValorGarantiaExecucao = new Moeda();
$obTxtValorGarantiaExecucao->setNull(false);
$obTxtValorGarantiaExecucao->setMaxLength('');
if ($nmValorGarantiaExecucao == '') {
    $nmValorGarantiaExecucao = '0,00';
}
$obTxtValorGarantiaExecucao->setValue($nmValorGarantiaExecucao);
$obTxtValorGarantiaExecucao->setName('nmValorGarantiaExecucao');
$obTxtValorGarantiaExecucao->setRotulo('Valor da Garantia de Execução');
$obTxtValorGarantiaExecucao->setTitle('Informe o valor da garantia de execução.');

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setId     ( "stJustificativa" );
$obTxtJustificativa->setName   ( "stJustificativa" );
$obTxtJustificativa->setRotulo ( "Justificativa" );
$obTxtJustificativa->setTitle  ( "Informe a Justificativa." );
$obTxtJustificativa->setNull ( false );
$obTxtJustificativa->setRows ( 2 );
$obTxtJustificativa->setCols ( 70 );
$obTxtJustificativa->setMaxCaracteres ( 250 );
$obTxtJustificativa->setValue ( $stJustificativa );

$obTxtRazao = new TextArea;
$obTxtRazao->setId     ( "stRazao" );
$obTxtRazao->setName   ( "stRazao" );
$obTxtRazao->setRotulo ( "Razão" );
$obTxtRazao->setTitle  ( "Informe a razão." );
$obTxtRazao->setNull ( false );
$obTxtRazao->setRows ( 2 );
$obTxtRazao->setCols ( 70 );
$obTxtRazao->setMaxCaracteres ( 250 );
$obTxtRazao->setValue ( $stRazao );

$obTxtFundamentacaoLegal = new TextArea;
$obTxtFundamentacaoLegal->setId     ( "stFundamentacaoLegal" );
$obTxtFundamentacaoLegal->setName   ( "stFundamentacaoLegal" );
$obTxtFundamentacaoLegal->setRotulo ( "Fundamentação Legal" );
$obTxtFundamentacaoLegal->setTitle  ( "Informe a Fundamentação Legal." );
$obTxtFundamentacaoLegal->setNull ( false );
$obTxtFundamentacaoLegal->setRows ( 2 );
$obTxtFundamentacaoLegal->setCols ( 70 );
$obTxtFundamentacaoLegal->setMaxCaracteres ( 250 );
$obTxtFundamentacaoLegal->setValue ( $stFundamentacaoLegal );

$obChkImprimirContrato = new CheckBox;
$obChkImprimirContrato->setRotulo('Imprimir Contrato');
$obChkImprimirContrato->setName('boImprimirContrato');
$obChkImprimirContrato->setTitle('Deseja Imprimir o contrato?');

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull( true );
$obVeiculoPublicidade->obCampoCod->setNull( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataPublicacao" );
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( date('d/m/Y') );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setObrigatorioBarra( true );
$obDataPublicacao->setTitle( "Informe a data de publicação." );

$obNumeroPublicacao = new Inteiro();
$obNumeroPublicacao->setId   ( "inNumPublicacao" );
$obNumeroPublicacao->setName ( "inNumPublicacao" );
$obNumeroPublicacao->setValue( '' );
$obNumeroPublicacao->setRotulo( "Número Publicação" );
$obNumeroPublicacao->setObrigatorioBarra( false	);
$obNumeroPublicacao->setTitle( "Informe o Número da Publicação " );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName('HdnCodDocumento');
$obHdnCodDocumento->setId('HdnCodDocumento');

$obISelectDocumento = new ISelectDocumento;
$obISelectDocumento->setObrigatorioBarra(true);

$obDataEmissao = new Data();
$obDataEmissao->setName('stDataEmissao');
$obDataEmissao->setId('stDataEmissao');
$obDataEmissao->setRotulo('Data de Emissão');
$obDataEmissao->setValue($request->get('stDataEmissao'));
$obDataEmissao->setObrigatorioBarra(true);
$obDataEmissao->obEvento->setOnChange("bloqueiaDesbloqueiaCampos(this);formataDiasValidosDocumento();");

$obDataValidade = new Data();
$obDataValidade->setName ( "stDataValidade" );
$obDataValidade->setId ( "stDataValidade" );
$obDataValidade->setValue( $request->get('stDataValidade') );
$obDataValidade->setRotulo( "Data de Validade" );
$obDataValidade->setTitle( "Informe a Data de Validade do Documento." );
$obDataValidade->obEvento->setOnChange("if (verificaData(this)) { if (validaData(this)) { formataDiasValidosDocumento(); } } else { jQuery(this).val(''); jQuery('#inNumDiasValido').val(''); }");
$obDataValidade->setObrigatorioBarra(true);
if ($dt_emissao == "") {
    $obDataValidade->setDisabled(true);
} else {
    $obDataValidade->setDisabled(false);
}

$obTxtNumDiasVcto = new TextBox;
$obTxtNumDiasVcto->setName  ( "inNumDiasValido" );
$obTxtNumDiasVcto->setId  ( "inNumDiasValido" );
$obTxtNumDiasVcto->setRotulo( "Dias para Vencimento" );
$obTxtNumDiasVcto->setTitle ( "Informe o número de dias para o vencimento do documento." );
$obTxtNumDiasVcto->setValue ( $request->get('inNumDiasValido') );
$obTxtNumDiasVcto->setMaxLength(4);
$obTxtNumDiasVcto->setInteiro(true);
$obTxtNumDiasVcto->setObrigatorioBarra( false );
if ($dt_emissao == "") {
    $obTxtNumDiasVcto->setDisabled(true);
} else {
    $obTxtNumDiasVcto->setDisabled(false);
}
$obTxtNumDiasVcto->obEvento->setOnBlur('formataDataValidaDocumento()');

$obTxtNumDocumento = new TextBox;
$obTxtNumDocumento->setName  ( "stNumDocumento" );
$obTxtNumDocumento->setId  ( "stNumDocumento" );
$obTxtNumDocumento->setRotulo( "Número do Documento" );
$obTxtNumDocumento->setTitle ( "Informe o número do documento." );
$obTxtNumDocumento->setSize ( 30 );
$obTxtNumDocumento->setMaxLength( 30 );
$obTxtNumDocumento->setObrigatorioBarra( true );
$obTxtNumDocumento->setInteiro(true);

$obSpnAtributosDocumento = new Span;
$obSpnAtributosDocumento->setId('spnAtributosDocumento');

$obSpnListaDocumentos = new Span;
$obSpnListaDocumentos->setId('spnListaDocumentos');

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ( "stObservacao"                               );
$obTxtObservacao->setName   ( "stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setObrigatorioBarra( false                               );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMaxCaracteres( 80 );

/****************************************************************************************************************************/
$obFileArquivo = new FileBox;
$obFileArquivo->setId     ( "stArquivo"            );
$obFileArquivo->setName   ( "stArquivo[]"          );
$obFileArquivo->setValue  ( ""                     );
$obFileArquivo->setRotulo ( "Arquivo"              );
$obFileArquivo->setTitle  ( "Selecione o arquivo." );
$obFileArquivo->setSize( "50" );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirArquivo = new Button;
$obBtnIncluirArquivo->setValue             ( "Incluir arquivo" );
$obBtnIncluirArquivo->setId                ( "incluiArquivo"   );
$obBtnIncluirArquivo->obEvento->setOnClick ( "montaParametrosGET('addArquivo', 'id, stArquivo');" );

//Span da Listagem de veí­culos de Publicação Utilizados
$obSpnListaInputFile = new Span;
$obSpnListaInputFile->setID("spnListaInputFile");

//Span da Listagem de veí­culos de Publicação Utilizados
$obSpnListaArquivo = new Span;
$obSpnListaArquivo->setID("spnListaArquivos");
/****************************************************************************************************************************/

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-03.05.22" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados do Contrato"   );

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente( $obCmbTipoContrato );
}else{
     $obFormulario->addComponente ( $obLblTipoContrato );
}

$obFormulario->addComponente( $obCmbTipoInstrumento );

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente( $obTxtExercicioContrato );
    
    $obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao );
    $obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade );
    $obFormulario->addComponente( $obTxtNumeroContrato );
    
    $obMontaCompraDireta->geraFormulario( $obFormulario );
    $obFormulario->addComponente     ( $obCmbTipoObjeto );
}

if ($stAcao == 'alterarCD') {
   $obFormulario->addComponente ( $obLblExercicioContrato );
   $obFormulario->addComponente ( $obLblExercicioLicitacao );
   $obFormulario->addComponente ( $obLblEntidade );
   
   $obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao );
   $obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade );

   $obFormulario->addComponente  ( $obLblNumeroCompraDireta );
   $obFormulario->addComponente  ( $obCmbTipoObjeto );
   
   $obFormulario->addHidden      ( $obHdnCodEntidade );
   $obFormulario->addHidden      ( $obHdnCodModalidade );
   

   $obFormulario->addComponente  ( $obTxtNumeroContrato );
   $obFormulario->addHidden      ( $obHdnNumeroContrato );
   $obFormulario->addHidden      ( $obHdnNumContrato );
   $obFormulario->addHidden      ( $obHdnExercicio );
}

$obFormulario->addComponente     ( $obLblDescObjeto );
$obFormulario->addHidden         ( $obHdnDescObjeto );
$obFormulario->addHidden         ( $obHdnCodVeiculo );

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente ( $obIPopUpCGM );
} else {
    $obFormulario->addComponente ( $obLblResponsavelJuridico );
    $obFormulario->addHidden     ( $obHdnResponsavelJuridico );
}

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente ( $obCmbContratado );
} else {
    $obFormulario->addComponente ( $obLblContratado );
    $obFormulario->addHidden     ( $obHdnContratado );
    $obFormulario->addHidden     ( $obHdnNomContratado );
    $obFormulario->addHidden     ( $obHdnNomRepresentante );
    //$obFormulario->addComponente   ( $obLblRepresentanteLegal );
    //$obFormulario->addHidden       ( $obHdnRepresentanteLegal );
    //$obFormulario->addHidden       ( $obHdnNomRepresentanteLegal );
}
$obFormulario->addComponente  ( $obCGMRepresentanteLegal );
$obFormulario->addComponente    ( $obTxtObjeto );
$obFormulario->addComponente    ( $obTxtFormaFornecimento );
$obFormulario->addComponente    ( $obTxtFormaPagamento );
$obFormulario->addComponente    ( $obCGMSignatario );
$obFormulario->addComponente    ( $obTxtPrazoExecucao );
$obFormulario->addComponente    ( $obTxtMultaInadimplemento );
$obFormulario->addComponente    ( $obTxtMultaRescisoria );

$obFormulario->addComponente  ( $obTxtDataAssinatura     );
$obFormulario->addComponente  ( $obTxtVencimento         );
$obFormulario->addComponente  ( $obTxtDataInicioExecucao );
$obFormulario->addComponente  ( $obTxtDataFimExecucao    );
$obFormulario->addHidden      ( $obHdnValorContrato );
$obFormulario->addComponente  ( $obTxtValorContrato );
$obFormulario->addComponente  ( $obCmbTipoGarantia );
$obFormulario->addComponente  ( $obTxtValorGarantiaExecucao );

$obFormulario->addComponente    ( $obTxtJustificativa );
$obFormulario->addComponente    ( $obTxtRazao );
$obFormulario->addComponente    ( $obTxtFundamentacaoLegal );

$obFormulario->addComponente  ( $obChkImprimirContrato );
$obFormulario->addTitulo      ( "Dados dos Documentos Exigidos"   );
$obFormulario->addComponente  ( $obISelectDocumento   );
$obFormulario->addComponente  ( $obTxtNumDocumento    );
$obFormulario->addHidden      ( $obHdnCodDocumento    );
$obFormulario->addComponente  ( $obDataEmissao     );
$obFormulario->addComponente  ( $obTxtNumDiasVcto   );
$obFormulario->addComponente  ( $obDataValidade    );
$obFormulario->IncluirAlterar ( 'Documentos', array( $obISelectDocumento, $obDataEmissao, $obDataValidade, $obTxtNumDocumento, $obTxtNumDiasVcto) );
$obFormulario->addSpan        ( $obSpnListaDocumentos );
$obFormulario->addTitulo      ( 'Veículo de Publicação' );
$obFormulario->addComponente  ( $obVeiculoPublicidade );
$obFormulario->addComponente  ( $obDataPublicacao );
$obFormulario->addComponente  ( $obNumeroPublicacao );

$obFormulario->addComponente  ( $obTxtObservacao );
$obFormulario->defineBarra    ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan        ( $obSpnListaVeiculo );

$obFormulario->addTitulo      ( 'Arquivos Digitais' );
$obFormulario->addComponente  ( $obFileArquivo );
$obFormulario->addSpan        ( $obSpnListaInputFile );
$obFormulario->defineBarra    ( array( $obBtnIncluirArquivo) );
$obFormulario->addSpan        ( $obSpnListaArquivo );

if ($stAcao == 'incluirCD') {
    $obBtnOk = new Ok;
    $obBtnLimpar = new Button;
    $obBtnLimpar->setName( "Limpar" );
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->setTipo( "Reset" );
    $obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );
    $obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );
} else {
    foreach ($_REQUEST as $chave =>$valor) {
        $param.= "&".$chave."=".$valor;
    }
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$param;
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show();

$jsOnLoad = "";
if ($stAcao == 'alterarCD') {
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaDocumentos');";
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculos');";
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaArquivos&num_contrato=".$inNumContrato."&exercicio=".$inExercicioContrato."&cod_entidade=".$inCodEntidade."');";
    
  if($inNumUnidade != '') {
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumOrgao=".$inNumOrgao."&inNumUnidade=".$inNumUnidade."', 'MontaUnidade'); \n";
  }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
