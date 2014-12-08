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
    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : 04/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    $Id: FMManterProcessoLicitatorio.php 60659 2014-11-06 16:17:39Z diogo.zarpelon $

    * Casos de uso : uc-03.04.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
include_once CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectCriterioJulgamento.class.php";
include_once CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php";
include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectComissao.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectComissaoEquipeApoio.class.php";
include_once CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectDocumento.class.php";
include_once TLIC."TLicitacaoLicitacaoAnulada.class.php";
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';

//Definições padrões do framework
$stPrograma = "ManterProcessoLicitatorio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once $pgJs;

Sessao::write('arDocumentos', array());
Sessao::write('arMembros', array());
Sessao::write('arMembro', array());
Sessao::write('arDocumentosExcluidos', array());
Sessao::write('arMembrosExcluidos', array());
Sessao::write('arMembrosIncluidos', array());
Sessao::remove('arMembroAdicionalExcluido');
Sessao::remove('arMembroAdicionalIncluido');

$stAcao = $request->get('stAcao');

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
$jsOnload = '';

if (isset($_REQUEST['inCodLicitacao'])){
    include_once(TLIC."TLicitacaoEdital.class.php");
    $obLicitacaoEdital = new TLicitacaoEdital;
    $obLicitacaoEdital->recuperaTodos($rsLicitacaoEdital, " WHERE cod_licitacao = ".$_REQUEST['inCodLicitacao']." AND
                                                                cod_modalidade = ".substr($_REQUEST['stModalidade'],0,1)." AND
                                                                cod_entidade IN (".substr($_REQUEST['stEntidade'],0,1).") AND
                                                                exercicio = '".Sessao::getExercicio()."'"
                                    );
    
    $boEdital = Sessao::write('boEdital', $rsLicitacaoEdital->getNumLinhas() > 0 ? true : false);
}

//Verifica se ja houve julgamento
$compraJulgamento = false;
if ($stAcao == 'alterar') {
    include_once( CAM_GP_COM_MAPEAMENTO."TComprasMapaCotacao.class.php" );
    $arMapaCompra = explode('/', $_REQUEST['stMapaCompra']);

    $obTComprasMapaCotacao = new TComprasMapaCotacao;
    $obTComprasMapaCotacao->setDado('cod_mapa'      , $arMapaCompra[0]      );
    $obTComprasMapaCotacao->setDado('exercicio_mapa', $arMapaCompra[1]       );
    $obTComprasMapaCotacao->recuperaPorChave($rsRecordSet);

    if ($rsRecordSet->getNumLinhas() > 0) {
        include_once( CAM_GP_COM_MAPEAMENTO."TComprasJulgamento.class.php" );
        $obTComprasJulgamento = new TComprasJulgamento;
        $obTComprasJulgamento->setDado('exercicio'  , $rsRecordSet->getCampo('exercicio_cotacao'));
        $obTComprasJulgamento->setDado('cod_cotacao', $rsRecordSet->getCampo('cod_cotacao')      );
        $obTComprasJulgamento->recuperaPorChave($rsRecordSet);
        if ($rsRecordSet->getNumLinhas() > 0) {
            include_once( CAM_GP_COM_MAPEAMENTO."TComprasJulgamentoItem.class.php" );
            $obTComprasJulgamentoItem = new TComprasJulgamentoItem;
            $obTComprasJulgamentoItem->setDado('exercicio'  , $rsRecordSet->getCampo('exercicio')   );
            $obTComprasJulgamentoItem->setDado('cod_cotacao', $rsRecordSet->getCampo('cod_cotacao') );
            $obTComprasJulgamentoItem->recuperaPorChave($rsRecordSet);
            if ($rsRecordSet->getNumLinhas() > 0) {
                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoItemPreEmpenhoJulgamento.class.php" );
                $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento;
                $stFiltro  = " WHERE exercicio_julgamento = '".$rsRecordSet->getCampo('exercicio')."'";
                $stFiltro .= "   AND cod_cotacao          =  ".$rsRecordSet->getCampo('cod_cotacao');
                $stFiltro .= "   AND cod_item             =  ".$rsRecordSet->getCampo('cod_item');
                $stFiltro .= "   AND lote                 =  ".$rsRecordSet->getCampo('lote');
                $stFiltro .= "   AND cgm_fornecedor       =  ".$rsRecordSet->getCampo('cgm_fornecedor');
                $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos($rsRecordSet, $stFiltro);
                if ($rsRecordSet->getNumLinhas() > 0) {
                    $compraJulgamento = true;
                }
            }
        }
    }
}
// $compraJulgamento = false;
if ($stAcao == 'alterar') {
    $stUnidadeOrcamentaria = $_REQUEST['stUnidadeOrcamentaria'];
    $jsOnload = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stEntidade=".$_REQUEST['stEntidade']."&stProcesso=".$_REQUEST['stProcesso']."&stMapaCompra=".$_REQUEST['stMapaCompra']."&inCodLicitacao=".$_REQUEST['inCodLicitacao']."&stModalidade=".$_REQUEST['stModalidade']."&stCodObjeto=".$_REQUEST['stCodObjeto']."&inCodTipoObjeto=".$_REQUEST['inCodTipoObjeto']."&inCodComissao=".$_REQUEST['inCodComissao']."&inCodTipoLicitacao=".$_REQUEST['inCodTipoLicitacao']."&inCodCriterio=".$_REQUEST['inCodCriterio']."&vlCotado=".$_REQUEST['vlCotado']."&stExercicioLicitacao=".$_REQUEST['stExercicioLicitacao']."&inCodRegime=".$_REQUEST['inCodRegime']."&boJulgamento=".$compraJulgamento."','preencheAlteracao');\n";
}

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o Hidden de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == 'incluir') {
    include_once CAM_GP_COM_MAPEAMENTO.'TComprasConfiguracao.class.php';

    $obTConfiguracao = new TComprasConfiguracao;
    $obTConfiguracao->setDado('parametro', 'numeracao_automatica');
    $obTConfiguracao->recuperaPorChave($rsConfiguracao);
    $boIdLicitacaoAutomatica = $rsConfiguracao->getCampo('valor');

    // Caso o parâmetro não for true, constroi o campo para o usuário informar o cód. da licitação.
    if ($boIdLicitacaoAutomatica != 't') {
        $obCodLicitacao = new Inteiro();
        $obCodLicitacao->setId    ('inCodLicitacaoImplantacao');
        $obCodLicitacao->setName  ('inCodLicitacaoImplantacao');
        $obCodLicitacao->setRotulo('Código da Licitação');
        $obCodLicitacao->setTitle ('Informe o código da licitação.');
        $obCodLicitacao->setNull  (false);
    }
}

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
if ($stAcao == 'incluir') {
    $stJsDataContabil = "if (this.value != '') { ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+this.value+'','recuperaUltimaDataContabil'); } else { if (jQuery('#stDtLicitacao')) { jQuery('#stDtLicitacao').val(''); }}";
    $obISelectEntidadeUsuario->obSelect->obEvento->setOnChange ($stJsDataContabil);
    $obISelectEntidadeUsuario->obTextBox->obEvento->setOnChange($stJsDataContabil);
}
$obISelectEntidadeUsuario->setNull(false);
if(!$compraJulgamento){
    $obDtLicitacao = new Data;
    $obDtLicitacao->setName               ( 'stDtLicitacao'                                                           );
    $obDtLicitacao->setId                 ( 'stDtLicitacao'                                                           );
    $obDtLicitacao->setRotulo             ( 'Data da Licitação'                                                       );
    $obDtLicitacao->setTitle              ( 'Informe a data de licitação.'                                            );
    $obDtLicitacao->setValue              ( $request->get('stDtLicitacao')                                            );
    $obDtLicitacao->setNull               ( false                                                                     );
    $obDtLicitacao->obEvento->setOnBlur   ( $obDtLicitacao->obEvento->getOnBlur()."montaParametrosGET('validaDtLicitacao','stDtLicitacao, stMapaCompras');" );
}
//Define o objeto de popup de busca por CGM
$obMembroAdicional = new IPopUpCGM($obForm);

//método setRotulo usado para definir o rótulo (nome que aparece ao lado do campo) no formulário
$obMembroAdicional->setRotulo( "Membro Adicional" );
$obMembroAdicional->setTitle ( 'Selecione o CGM do membro adicional.' );
$obMembroAdicional->setName  ( "stMembroAdicional" );

//método booleano setNull usado para forçar ou não o campo a ser obrigatório (true = null)
$obMembroAdicional->setNull  (true);
$obMembroAdicional->setValue ( "" );
$obMembroAdicional->setObrigatorioBarra(true);

//recuperar o valor total da referência...
// = soma do total de todos itens da cotação ou precisa calcular atráves do mapa de compras.

//Define o Label de valor total da referência
$obLblValorReferencia = new Label();
$obLblValorReferencia->setName('flValorReferencia');
$obLblValorReferencia->setId( 'stValorReferencia' );
$obLblValorReferencia->setRotulo('Valor Total de Referência');
if (!isset($stValorReferencia)) {
    $stValorReferencia = '';
}
$obLblValorReferencia->setValue( $stValorReferencia == '' ? "0,00" : number_format( $stValorReferencia , 2 , ',' , '.' ) );

//// define o Label para tipo de Cotação
$obLblTipoCotacao = new Label();
$obLblTipoCotacao->setName ( 'txtTipoCotacao' );
$obLblTipoCotacao->setId   ( 'stTipoCotacao' );
$obLblTipoCotacao->setRotulo ( 'Tipo Cotação' );

$obHdnTipoCotacao = new Hidden;
$obHdnTipoCotacao->setName  ( 'inCodTipoCotacao' );
$obHdnTipoCotacao->setValue ( '' );

$obHdnValorReferencia = new Hidden();
$obHdnValorReferencia->setName('stValorReferencial');
$obHdnValorReferencia->setId('stValorReferencial');
$obHdnValorReferencia->setValue( $stValorReferencia == '' ? "0,00" : number_format( $stValorReferencia , 2 , ',' , '.' ) );

//Define objeto de select modalidade licitacao
if ($stAcao == 'incluir') {

    include_once(CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php");
    $obComprasModalidade = new TComprasModalidade();
    $rsRecordSet = new RecordSet;
    //$stFiltro = "	WHERE	cod_modalidade NOT IN(8,9)  "; pedido para ser liberado o 8 e 9 - ticket #12862

    // Solicitado pelo Gelson que não aparece essas modalidades por enquanto.
    //$stFiltro = " WHERE	cod_modalidade NOT IN(4,5,6,7)  ";
    $stFiltro = " WHERE	cod_modalidade NOT IN(4,5)  ";

    $obComprasModalidade->recuperaTodos($rsRecordSet,$stFiltro);

    $obISelectModalidadeLicitacao = new Select();
    $obISelectModalidadeLicitacao->setRotulo     ("Modalidade"                            );
    $obISelectModalidadeLicitacao->setTitle      ("Selecione a modalidade."               );
    $obISelectModalidadeLicitacao->setName       ("inCodModalidade"                       );
    $obISelectModalidadeLicitacao->setId         ("inCodModalidade"                       );
    $obISelectModalidadeLicitacao->setNull       (true                                    );
    $obISelectModalidadeLicitacao->setCampoID    ("cod_modalidade"                        );
    $obISelectModalidadeLicitacao->addOption     ("","Selecione"                          );
    $obISelectModalidadeLicitacao->setCampoDesc  ("[cod_modalidade] - [descricao]"        );
    $obISelectModalidadeLicitacao->preencheCombo ($rsRecordSet                            );
    $obISelectModalidadeLicitacao->setNull       ( false                                  );
}

if(!$boEdital) {
    $obPopUpObjeto = new IPopUpObjeto($obForm);
    $obPopUpObjeto->setNull(false);
} else {
    $obTComprasMapa = new TComprasMapa;
    $obTComprasMapa->setDado('cod_mapa'  , $inCodMapa);
    $obTComprasMapa->setDado('exercicio' , $stExercicioMapa);
    $obTComprasMapa->recuperaMapaObjeto($rsMapa);
    
    $mapaCompras     = explode("/", $_REQUEST['stMapaCompra']);
    $inCodMapa       = $mapaCompras[0];
    $stExercicioMapa = $mapaCompras[1];
    $obTComprasMapa->setDado('cod_mapa'  , $inCodMapa);
    $obTComprasMapa->setDado('exercicio' , $stExercicioMapa);
    $obTComprasMapa->recuperaMapaObjeto($rsMapa);    
    
    $obLblObjeto = new Label;
    $obLblObjeto->setName('stObjeto');
    $obLblObjeto->setId('stObjeto');
    $obLblObjeto->setRotulo('Objeto');
    $obLblObjeto->setValue($rsMapa->getCampo('cod_objeto').' - '.$rsMapa->getCampo('descricao'));
    
    $obHdnObjeto = new Hidden();
    $obHdnObjeto->setName('hdnObjeto');
    $obHdnObjeto->setId('hdnObjeto');
    $obHdnObjeto->setValue($rsMapa->getCampo('cod_objeto'));
}

$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setTarget('oculto');
$obIMontaUnidadeOrcamentaria->setRotulo('Unidade Executora');
$obIMontaUnidadeOrcamentaria->setValue( $stUnidadeOrcamentaria );
$obIMontaUnidadeOrcamentaria->setCodOrgao('');
$obIMontaUnidadeOrcamentaria->setCodUnidade('');
$obIMontaUnidadeOrcamentaria->setActionPosterior($pgProc);
$obIMontaUnidadeOrcamentaria->setNull(false);
$obIMontaUnidadeOrcamentaria->setTitle("Código do Orgão/Unidade responsável pela abertura e execução do procedimento Licitatório.");

//definição objeto de popupcgm
$obIpopUpCgm = new IPopUpCGM($obForm);
$obIpopUpCgm->setRotulo("");
$obIpopUpCgm->setTitle("Informe o fornecedor.");

//definição do objeto de textBoxSelectdocumentos
$obISelectDocumento = new ISelectDocumento();
$obISelectDocumento->setNull(false);
$obISelectDocumento->setObrigatorioBarra(true);

//Define objeto de select critério julgamento
$obISelectCriterioJulgamento = new ISelectCriterioJulgamento();
$obISelectCriterioJulgamento->setNull(false);

//Define objeto de select tipo Objeto
$obISelectTipoObjeto = new ISelectTipoObjeto();
$obISelectTipoObjeto->setNull(false);
$obISelectTipoObjeto->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodTipoObjeto='+this.value,'recuperaRegimeExecucaoObra');");

// Define objeto span para objeto Regime de Execução de Obras
$obSpnRegime = new Span();
$obSpnRegime->setId( "spnRegime" );

// Define objeto span para objeto valor maximo/minimo
$obSpnMaxMin = new Span();
$obSpnMaxMin->setId( "spnMaxMin" );

$obPopUpProcesso = new IPopUpProcesso($obForm);
$obPopUpProcesso->setRotulo("Processo Administrativo");
$obPopUpProcesso->setValidar(true);

$stProcesso = explode ("/",$request->get('stProcesso'));

$obPopUpProcesso->setValue($stProcesso[0]);
$obPopUpProcesso->setNull(false);

$obISelectComissao = new ISelectComissao(true);
$obISelectComissao->setNull(false);
$obISelectComissao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodComissao='+this.value,'comissaoMembro');");
$obISelectComissao->setName ( 'inCodComissao' ) ;
$obISelectComissao->setId   ( 'inCodComissao' ) ;

$obISelectComissaoEquipeApoio = new ISelectComissaoEquipeApoio;
$obISelectComissaoEquipeApoio->setName('inCodComissaoApoio');
$obISelectComissaoEquipeApoio->setId('inCodComissaoApoio');
$obISelectComissaoEquipeApoio->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodComissaoApoio='+this.value,'comissaoMembroApoio');");
$obISelectComissaoEquipeApoio->setTitle( 'Selecione a equipe de apoio.' );

$obSpnMembros = new Span;
$obSpnMembros->setId ( 'spnMembros' );

$obSpnMembroAdicional = new Span();
$obSpnMembroAdicional->setId( "spnMembroAdicional" );

$obSpnDocumento = new Span();
$obSpnDocumento->setId( "spnDocumento" );

$obPopUpMapa = new IPopUpMapaCompras($obForm);
$obPopUpMapa->setTipoBusca ( 'processoLicitatorio' );
$obPopUpMapa->setExercicio(Sessao::getExercicio());
$obPopUpMapa->setNull(false);
$obPopUpMapa->setAutEmp(true);
$obPopUpMapa->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&mapaCompras='+this.value,'vlTotalReferencia');");
$obPopUpMapa->obCampoCod->obEvento->setOnBlur($obPopUpMapa->obCampoCod->obEvento->getOnBlur()."montaParametrosGET('validaMapa','stDtLicitacao, stMapaCompras');");
$obPopUpMapa->obCampoCod->obEvento->setOnBlur($obPopUpMapa->obCampoCod->obEvento->getOnBlur()."montaParametrosGET('montaItensAlterar','stMapaCompras');");

$obHdnLicitacao = new Hidden;
$obHdnLicitacao->setName('hdnCodLicitacao');
$obHdnLicitacao->setValue($request->get('inCodLicitacao'));

$obHdnstExercicioLicitacao = new Hidden;
$obHdnstExercicioLicitacao->setName('hdnExercicioLicitacao');
$obHdnstExercicioLicitacao->setValue($request->get('stExercicioLicitacao'));

if ($stAcao != "incluir") {

    $obLblEntidade = new Label();
    $obLblEntidade->setRotulo('Entidade');
    $obLblEntidade->setValue($_REQUEST['stEntidade']);
    $obLblEntidade->setName ( 'obTeste' );

    $obHdnEntidade = new Hidden();
    $obHdnEntidade->setName('hdnEntidade');
    $obHdnEntidade->setValue($_REQUEST['stEntidade']);

    $obLblModalidade = new Label();
    $obLblModalidade->setRotulo('Modalidade');
    $obLblModalidade->setValue($_REQUEST['stModalidade']);

    $obHdnModalidade = new Hidden();
    $obHdnModalidade->setName('hdnModalidade');
    $obHdnModalidade->setValue($_REQUEST['stModalidade']);

    $obLblLicitacao = new Label();
    $obLblLicitacao->setRotulo('Código da Licitação');
    $obLblLicitacao->setValue($_REQUEST['inCodLicitacao']);
    
    
    if ( $compraJulgamento ) {
        $obLblProcessoAdm = new Label();
        $obLblProcessoAdm->setRotulo('Processo Administrativo');
        $obLblProcessoAdm->setValue($_REQUEST['stProcesso']);
        
        $obHdnProcessoAdm = new Hidden();
        $obHdnProcessoAdm->setName('stChaveProcesso');
        $obHdnProcessoAdm->setValue($stProcesso[0]."/".$stProcesso[1]);
        
        $obHdnCodModalidade = new Hidden();
        $obHdnCodModalidade->setName('inCodModalidade');
        $obHdnCodModalidade->setValue( $_REQUEST['inCodModalidade']);
        
        $obLblMapaCompra = new Label();
        $obLblMapaCompra->setRotulo('Mapa de Compras');
        $obLblMapaCompra->setValue($_REQUEST['stMapaCompra']);
        
        include_once(TLIC."TLicitacaoLicitacao.class.php");
        $obLicitacaoLicitacao = new TLicitacaoLicitacao;
        $obLicitacaoLicitacao->setDado('cod_licitacao' , $_REQUEST['inCodLicitacao']   );
        $obLicitacaoLicitacao->setDado('cod_modalidade', $_REQUEST['inCodModalidade']  );
        $obLicitacaoLicitacao->setDado('cod_entidade'  , trim($entidade[0])            );
        $obLicitacaoLicitacao->setDado('exercicio'     , Sessao::getExercicio()        );
        $obLicitacaoLicitacao->recuperaPorChave($rsLicitacao);
    
        $dtLicitacao = SistemaLegado::dataToBr(substr($rsLicitacao->getCampo("timestamp"),0,10));
        
        $obLblDtLicitacao = new Label();
        $obLblDtLicitacao->setId('stDtLicitacao');
        $obLblDtLicitacao->setRotulo('Data da Licitação');
        $obLblDtLicitacao->setValue($dtLicitacao);
        
        $obHdnDtLicitacao = new Hidden();
        $obHdnDtLicitacao->setName('stDtLicitacao');
        $obHdnDtLicitacao->setValue($dtLicitacao);
        
        $obHdnMapaCompra = new Hidden();
        $obHdnMapaCompra->setName('hdnMapaCompra');
        $obHdnMapaCompra->setValue($_REQUEST['stMapaCompra']);
        
        include_once(CAM_GP_COM_MAPEAMENTO . "../../../licitacao/classes/mapeamento/TLicitacaoCriterioJulgamento.class.php");
        $obMapeamento   = new TLicitacaoCriterioJulgamento();
        $obMapeamento->setDado('cod_criterio', $_REQUEST['inCodCriterio']);
        $obMapeamento->recuperaPorChave( $rsRecordSet );
        
        $obLblCriterioJulg = new Label();
        $obLblCriterioJulg->setRotulo('Critério do Julgamento');
        $obLblCriterioJulg->setValue($rsRecordSet->getCampo('descricao'));
        
        $obHdnCriterioJulg = new Hidden();
        $obHdnCriterioJulg->setName('inCodCriterio');
        $obHdnCriterioJulg->setValue($rsRecordSet->getCampo('cod_criterio'));
    }
    if ($stAcao == "anular") {
        $obLblProcesso = new Label();
        $obLblProcesso->setRotulo('Processo Administrativo');
        $obLblProcesso->setValue($stProcesso[0]."/".$stProcesso[1]);
        $obHdnProcesso = new Hidden();
        $obHdnProcesso->setName('hdnProcesso');
        $obHdnProcesso->setValue($stProcesso[0]."/".$stProcesso[1]);

        $obLblMapaCompra = new Label();
        $obLblMapaCompra->setRotulo('Mapa de Compras');
        $obLblMapaCompra->setValue($_REQUEST['stMapaCompra']);

        $obHdnMapaCompra = new Hidden();
        $obHdnMapaCompra->setName('hdnMapaCompra');
        $obHdnMapaCompra->setValue($_REQUEST['stMapaCompra']);

        $obTLicitacaoLicitacaoAnula = new TLicitacaoLicitacaoAnulada();
        $obTLicitacaoLicitacaoAnula->setDado( 'cod_licitacao', $request->get('inCodLicitacao'));
        $obTLicitacaoLicitacaoAnula->setDado( 'cod_entidade', $request->get('inCodEntidade'));
        $obTLicitacaoLicitacaoAnula->setDado( 'cod_modalidade', $request->get('inCodModalidade'));
        $obTLicitacaoLicitacaoAnula->setDado( 'exercicio', $request->get('stExercicioLicitacao'));
        $obTLicitacaoLicitacaoAnula->recuperaPorChave($rsLicitacaoLicitacaoAnula);
        
        $obRadioDeserta = new Radio;
        $obRadioDeserta->setRotulo     ('Motivo Anulação');
        $obRadioDeserta->setLabel      ('Deserta');
        $obRadioDeserta->setName       ('boMotivoAnulacao');
        $obRadioDeserta->setId         ('boMotivoAnulacao');
        $obRadioDeserta->setTitle      ('Informe o Motivo da Anulação da Licitação.');
        $obRadioDeserta->setValue      ('boDeserta');
        $obRadioDeserta->setNull       (false);
        $obRadioDeserta->setChecked    (($rsLicitacaoLicitacaoAnula->getCampo('deserta') == 1));
        
        $obRadioFracassada = new Radio;
        $obRadioFracassada->setLabel   ('Fracassada');
        $obRadioFracassada->setTitle   ('Informe o Motivo da Anulação da Licitação.');
        $obRadioFracassada->setName    ('boMotivoAnulacao');
        $obRadioFracassada->setId      ('boMotivoAnulacao');
        $obRadioFracassada->setValue   ('boFracassada');
        $obRadioFracassada->setNull    (true);
        $obRadioFracassada->setChecked (($rsLicitacaoLicitacaoAnula->getCampo('fracassada') == 1));
        
        $obRadioOutros = new Radio;
        $obRadioOutros->setLabel      ('Outros');
        $obRadioOutros->setName       ('boMotivoAnulacao');
        $obRadioOutros->setId         ('boMotivoAnulacao');
        $obRadioOutros->setTitle      ('Informe o Motivo da Anulação da Licitação.');
        $obRadioOutros->setValue      ('boOutros');
        $obRadioOutros->setNull       (true);
        $obRadioOutros->setChecked    (($rsLicitacaoLicitacaoAnula->getCampo('deserta') == 0 && $rsLicitacaoLicitacaoAnula->getCampo('fracassada') == 0));
                
        $obTextAreaJustificativa = new TextArea();
        $obTextAreaJustificativa->setRotulo('Justificativa');
        $obTextAreaJustificativa->setName('stJustificativa');
        $obTextAreaJustificativa->setNull(false);
        $obTextAreaJustificativa->setMaxCaracteres(200);
    }
}
$obSpnItens = new Span;
$obSpnItens->setId( 'spnItens' );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                          );
//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda         ("UC-03.05.15"                     );
$obFormulario->addHidden        ( $obHdnCtrl                       );
$obFormulario->addHidden        ( $obHdnAcao                       );
$obFormulario->addHidden        ( $obHdnValorReferencia            );
$obFormulario->addHidden        ( $obHdnLicitacao                  );
$obFormulario->addHidden        ( $obHdnstExercicioLicitacao       );
$obFormulario->addHidden        ( $obHdnTipoCotacao                );


if ($stAcao != 'incluir') {
    $obFormulario->addComponente ($obLblEntidade);
    $obFormulario->addHidden ($obHdnEntidade);
    $obFormulario->addComponente ($obLblLicitacao);
    $obFormulario->addHidden     ($obHdnModalidade);
}
if ($stAcao != 'anular' ) {
    $obFormulario->addTitulo        ( "Dados da Licitação"             );
    if ($stAcao == 'incluir') {
        if ($boIdLicitacaoAutomatica != 't') {
            $obFormulario->addComponente ( $obCodLicitacao                 );
        }
    }
    if($compraJulgamento){
        $obFormulario->addComponente    ( $obLblProcessoAdm             );
        $obFormulario->addComponente    ( $obLblMapaCompra              );
        $obFormulario->addHidden        ( $obHdnProcessoAdm             );
        $obFormulario->addHidden        ( $obHdnMapaCompra              );
        $obFormulario->addHidden        ( $obHdnCodModalidade           );
        $obFormulario->addHidden        ( $obHdnCriterioJulg            );
        $obFormulario->addHidden        ( $obHdnDtLicitacao             );
    }else{
        $obFormulario->addComponente    ( $obPopUpProcesso                 );
        $obFormulario->addComponente    ( $obPopUpMapa                     );
    }
    
    if ($stAcao == 'incluir')
        $obFormulario->addComponente( $obISelectEntidadeUsuario    	   );
        
    if ( $compraJulgamento) {
        $obFormulario->addComponente    ( $obLblDtLicitacao                   );
    }else{
        $obFormulario->addComponente    ( $obDtLicitacao                   );
    }
    $obFormulario->addComponente    ( $obLblValorReferencia            );
    $obFormulario->addComponente    ( $obLblTipoCotacao                );

    if ($stAcao == 'incluir') {
        $obFormulario->addComponente( $obISelectModalidadeLicitacao    );
    } else {
        $obFormulario->addComponente( $obLblModalidade		       );
    }
    
     if($compraJulgamento){
        $obFormulario->addComponente    ( $obLblCriterioJulg             );
    }else{
        $obFormulario->addComponente    ( $obISelectCriterioJulgamento     );
    }
    
    $obFormulario->addComponente    ( $obISelectTipoObjeto             );
    $obFormulario->addSpan          ( $obSpnRegime                     );
    
    if(!$boEdital) {
        $obFormulario->addComponente    ( $obPopUpObjeto );
    } else {
        $obFormulario->addComponente    ( $obLblObjeto );
        $obFormulario->addHidden        ( $obHdnObjeto );
    }
   
    $obIMontaUnidadeOrcamentaria->geraFormulario( $obFormulario );
    $obFormulario->addSpan   ( $obSpnItens );
    $obFormulario->addSpan          ( $obSpnMaxMin                     );
    $obFormulario->addTitulo        ( "Dados da Comissão de Licitação" );
    $obFormulario->addcomponente    ( $obISelectComissao               );
    $obFormulario->addcomponente    ( $obISelectComissaoEquipeApoio    );
    $obFormulario->addSpan          ( $obSpnMembros                    );
    $obFormulario->addTitulo        ( "Membro Adicional"               );
    $obFormulario->addComponente    ( $obMembroAdicional               );
    $obFormulario->Incluir          ('MembroAdicional', array ( $obMembroAdicional ),true);
    $obFormulario->addSpan          ( $obSpnMembroAdicional            );
    
    if(!$boEdital) {
        $obFormulario->addTitulo        ( "Documentos Exigidos"            );
        $obFormulario->addcomponente    ( $obISelectDocumento              );
        $obFormulario->Incluir ('Documento', array ($obISelectDocumento),true);
    }
    
    $obFormulario->addSpan          ( $obSpnDocumento          );    
} else {
    $obFormulario->addComponente ($obLblProcesso);
    $obFormulario->addHidden     ($obHdnProcesso);
    $obFormulario->addComponente ($obLblMapaCompra);
    $obFormulario->addHidden     ($obHdnMapaCompra);
    $obFormulario->addComponente ($obLblModalidade);
    $obFormulario->agrupaComponentes(array($obRadioDeserta,$obRadioFracassada,$obRadioOutros));
    $obFormulario->addComponente ($obTextAreaJustificativa);
    $obFormulario->addSpan   ( $obSpnItens );
}

 if ($stAcao == "alterar" || $stAcao == "anular") {
        $jsOnload .="ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao=". $request->get('stExercicioLicitacao')."&inCodEntidade=".$inCodEntidade."&inCodLicitacao=".$request->get('inCodEntidade')."&stMapaCompras=".$request->get('stMapaCompra')."&boAlteraAnula=true','montaItensAlterar');";
    }
 if ($stAcao == 'alterar' OR $stAcao == 'anular') {
   $obButtonVoltar = new Button;
   $obButtonVoltar->setName  ( "Voltar" );
   $obButtonVoltar->setValue ( "Voltar" );
   $obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");
   
   $obOk   = new Ok;

   $obFormulario->defineBarra( array($obOk, $obButtonVoltar ), "left", "" );
} else
   $obFormulario->OK();
$obFormulario->show();

if ($stAcao != "alterar") {
$jsOnload .= " jq(document).ready(function () {
    jq('#limpar').click(function () {
        montaParametrosGET('limpaListas')
    });
});";
}
if ($stAcao != "alterar") {
$jsOnload .= "
    // Desabilita o combo de Comissão de Apoio, até que seja escolhida uma Comissão.
    habilitaEquipeApoio(false);
    ";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
