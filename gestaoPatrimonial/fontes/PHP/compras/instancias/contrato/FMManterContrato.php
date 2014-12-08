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

$stAcao = $request->get('stAcao');
$inNumContrato = $request->get('inNumContrato');
$inCodEntidade = $request->get('inCodEntidade');

$obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
$obTLicitacaoTipoContrato->recuperaTodos( $rsTipoContrato );

if ($inNumContrato) {
  $obTContratoCompraDireta = new TComprasContratoCompraDireta();

  $stFiltro  =" AND contrato.num_contrato =".$inNumContrato;
  $stFiltro .=" AND contrato.cod_entidade =".$inCodEntidade;
  $stFiltro .=" AND contrato.exercicio = '".Sessao::getExercicio()."'";

  $obTContratoCompraDireta->recuperaContratosCompraDireta($rsContrato, $stFiltro);

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
  $nmValorContrato = number_format($rsContrato->getCampo('valor_contratado'),2,',','.');
  $stTipoContrato = $rsContrato->getCampo('tipo_descricao');
  $inExercicioContrato  = $rsContrato->getCampo('exercicio_contrato');
  $inExercicioCompra  = $rsContrato->getCampo('exercicio_compra_direta');

  $obTContratoDocumento = new TLicitacaoContratoDocumento;
  $obTContratoDocumento->setDado('num_contrato', $inNumContrato);
  $obTContratoDocumento->setDado('cod_entidade', $inCodEntidade);
  $obTContratoDocumento->setDado('exercicio', Sessao::getExercicio());
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
  $obTLicitacaoPublicacaoContrato->setDado('exercicio', Sessao::getExercicio() );
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
$nmValorContrato = (isset($nmValorContrato)) ? $nmValorContrato : '';
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
$obHdnValorContrato->setValue($nmValorContrato );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName('HdnCodDocumento');
$obHdnCodDocumento->setId  ('HdnCodDocumento');

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

    $obHdnNomRepresentante = new Hidden;
    $obHdnNomRepresentante->setName('stNomCGM');
    $obHdnNomRepresentante->setId  ('stNomCGM');
    $obHdnNomRepresentante->setValue($stNomCGM );
}

if ($stAcao == 'incluirCD') {

    $obMontaCompraDireta = new IMontaNumeroCompraDireta($obForm);
    $obMontaCompraDireta->setSelecionaAutomaticamenteCompraDireta(true);
    $obMontaCompraDireta->obISelectModalidade->obEvento->setOnBlur($obMontaCompraDireta->obISelectModalidade->obEvento->getOnBlur()."montaParametrosGET('preencheObjetoCompraDireta', 'stExercicioCompraDireta,inCodEntidade,inCodModalidade,inCodCompraDireta', false );" );
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

} else {

    $obLblNumeroCompraDireta= new Label;
    $obLblNumeroCompraDireta->setRotulo              ( "Número da Compra Direta" );
    $obLblNumeroCompraDireta->setValue               ( $inCodCompraDireta);
    $obHdnNumeroCompraDireta = new Hidden;
    $obHdnNumeroCompraDireta->setName                ( 'inCodCompraDireta');
    $obHdnNumeroCompraDireta->setValue               ( $inCodCompraDireta);
    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName                    ( 'inCodEntidade' );
    $obHdnCodEntidade->setValue                   ( $inCodEntidade );
    $obHdnCodModalidade = new Hidden;
    $obHdnCodModalidade->setName                    ( 'inCodModalidade');
    $obHdnCodModalidade->setValue                   ( $inCodModalidade );

    $obLblNumeroContrato = new Label;
    $obLblNumeroContrato->setRotulo ( "Número do Contrato");
    $obLblNumeroContrato->setValue ( $inNumContrato );
    $obHdnNumeroContrato = new Hidden;
    $obHdnNumeroContrato->setName ( 'inNumContrato' );
    $obHdnNumeroContrato->setValue ( $inNumContrato );

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

$obLblValorContrato = new Label;
$obLblValorContrato->setRotulo('Valor do Contrato');
$obLblValorContrato->setId('nmValorContrato');
$obLblValorContrato->setValue($nmValorContrato);

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
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.05.22");
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addTitulo        ( "Dados do Contrato"   );

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente( $obCmbTipoContrato );
    $obFormulario->addComponente( $obTxtExercicioContrato );
    $obMontaCompraDireta->geraFormulario( $obFormulario );
}

if ($stAcao == 'alterarCD') {
    $obFormulario->addComponente    ( $obLblTipoContrato );
    $obFormulario->addComponente    ( $obLblExercicioContrato );
    $obFormulario->addComponente    ( $obLblExercicioLicitacao );

    $obFormulario->addComponente     ( $obLblNumeroCompraDireta );
    $obFormulario->addHidden         ( $obHdnNumeroCompraDireta );
    $obFormulario->addHidden         ( $obHdnCodEntidade );
    $obFormulario->addHidden         ( $obHdnCodModalidade );
}
$obFormulario->addComponente    ( $obLblDescObjeto );
$obFormulario->addHidden        ( $obHdnDescObjeto );
$obFormulario->addHidden        ( $obHdnCodVeiculo );

if ($stAcao == 'alterarCD') {
    $obFormulario->addComponente    ( $obLblNumeroContrato );
    $obFormulario->addHidden        ( $obHdnNumeroContrato );
}

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente    ( $obIPopUpCGM );
} else {
    $obFormulario->addComponente    ( $obLblResponsavelJuridico );
    $obFormulario->addHidden        ( $obHdnResponsavelJuridico );
}

if ($stAcao == 'incluirCD') {
    $obFormulario->addComponente    ( $obCmbContratado );
} else {
    $obFormulario->addComponente    ( $obLblContratado );
    $obFormulario->addHidden       ( $obHdnContratado );
    $obFormulario->addHidden       ( $obHdnNomContratado );
    $obFormulario->addHidden       ( $obHdnNomRepresentante );
}

$obFormulario->addComponente    ( $obTxtDataAssinatura     );
$obFormulario->addComponente    ( $obTxtVencimento         );
$obFormulario->addComponente    ( $obTxtDataInicioExecucao );
$obFormulario->addComponente    ( $obTxtDataFimExecucao    );

$obFormulario->addHidden        ( $obHdnValorContrato );
$obFormulario->addComponente    ( $obLblValorContrato );
$obFormulario->addComponente    ( $obTxtValorGarantiaExecucao );
$obFormulario->addComponente    ( $obChkImprimirContrato );
$obFormulario->addTitulo        ( "Dados dos Documentos Exigidos"   );
$obFormulario->addComponente    ( $obISelectDocumento   );
$obFormulario->addComponente    ( $obTxtNumDocumento    );
$obFormulario->addHidden        ( $obHdnCodDocumento    );
$obFormulario->addComponente    ( $obDataEmissao     );
$obFormulario->addComponente    ( $obTxtNumDiasVcto   );
$obFormulario->addComponente    ( $obDataValidade    );
$obFormulario->IncluirAlterar   ( 'Documentos', array( $obISelectDocumento, $obDataEmissao, $obDataValidade, $obTxtNumDocumento, $obTxtNumDiasVcto) );
$obFormulario->addSpan          ( $obSpnListaDocumentos );
$obFormulario->addTitulo        ( 'Veículo de Publicação' );
$obFormulario->addComponente    ( $obVeiculoPublicidade );
$obFormulario->addComponente    ( $obDataPublicacao );
$obFormulario->addComponente    ( $obNumeroPublicacao );

$obFormulario->addComponente    ( $obTxtObservacao );
$obFormulario->defineBarra      ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan          ( $obSpnListaVeiculo );

$obFormulario->addTitulo        ( 'Arquivos Digitais' );
$obFormulario->addComponente    ( $obFileArquivo );
$obFormulario->addSpan          ( $obSpnListaInputFile );
$obFormulario->defineBarra      ( array( $obBtnIncluirArquivo) );
$obFormulario->addSpan          ( $obSpnListaArquivo );

if ($stAcao == 'incluirCD') {
    $obBtnOk = new Ok;
    $obBtnLimpar = new Button;
    $obBtnLimpar->setName( "Limpar" );
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->setTipo( "Reset" );
    $obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );
    $obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );
} else {

    $dadosFiltro = Sessao::read('dadosFiltro',$param);
    foreach ($dadosFiltro as $chave =>$valor) {
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
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
