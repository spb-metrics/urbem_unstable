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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28380 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-05 14:52:21 -0300 (Qua, 05 Mar 2008) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"  );
include_once(CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");


$stPrograma = "ManterNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsNorma = $rsTipoNorma = $rsAtributos = new RecordSet;
$obRegra = new RNorma;

$stExercicio = Sessao::getExercicio();

$stAcao = $request->get('stAcao');

$inCodNorma = $_REQUEST["inCodNorma"];

if ( (empty($stAcao)) || ($stAcao == "incluir")) {
    Sessao::write('stNormaAcao','incluir');

    $js.= "limpaCampos ();";

    $obRegra->obRTipoNorma->listar( $rsTipoNorma );

} elseif ($stAcao) {
    Sessao::write('stNormaAcao','incluir');
    Sessao::write('inCodNorma',$inCodNorma);
    
    $obRegra->setCodNorma( $_REQUEST['inCodNorma'] );
    $obRegra->consultar( $rsNorma );
    
    $stNomeNorma     = $obRegra->getNomeNorma();
    $inNumNorma      = $obRegra->getNumNorma();
    $stExercicio     = $obRegra->getExercicio();
    $stNomeTipoNorma = $obRegra->obRTipoNorma->getNomeTipoNorma();
    $inCodTipoNorma  = $obRegra->obRTipoNorma->getCodTipoNorma();

    $stDescricao     = $obRegra->getDescricaoNorma();
    $stDataPublicacao= $obRegra->getDataPublicacao();
    $stDataAssinatura= $obRegra->getDataAssinatura();
    $stDataTermino   = $obRegra->getDataTermino();
    $stLink          = $obRegra->getUrl();

    Sessao::write('stNormaLink',$stLink);

    $obRegra->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
    $obRegra->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$inCodTipoNorma, "cod_norma"=>$obRegra->getCodNorma()) );
    $obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

$obLblLink = new Label;
$obLblLink->setRotulo ( "Arquivo" );
$obLblLink->setName   ( "stlblLabel" );
$obLblLink->setValue  ( $stLink );
$obLblLink->setId     ( "spnlink" );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setName ("Atributo_");
$obMontaAtributos->setRecordSet( $rsAtributos );
$obMontaAtributos->recuperaValores();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( "" );

//Caso inclusão
$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo        ( "Tipo" );
$obTxtTipoNorma->setName          ( "inCodTipoNorma" );
$obTxtTipoNorma->setValue         ( $inCodTipoNorma );
$obTxtTipoNorma->setSize          ( 5 );
$obTxtTipoNorma->setMaxLength     ( 5 );
$obTxtTipoNorma->setInteiro       ( true  );
$obTxtTipoNorma->setNull          ( false );
$obTxtTipoNorma->obEvento->setOnChange("buscaValor('MontaAtributos');");
$obTxtTipoNorma->setTitle         ( "Selecione o Tipo" );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo        ( "Tipo" );
$obCmbTipoNorma->setName          ( "stNomeTipoNorma" );
$obCmbTipoNorma->setStyle         ( "width: 200px");
$obCmbTipoNorma->setCampoID       ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc     ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption        ( "", "Selecione" );
if ( $stAcao == "alteracao" )
    $obCmbTipoNorma->setValue         ( $inCodTipoNorma );
$obCmbTipoNorma->setNull          ( false );
$obCmbTipoNorma->preencheCombo    ( $rsTipoNorma );
$obCmbTipoNorma->obEvento->setOnChange("buscaValor('MontaAtributos');");
$obCmbTipoNorma->setTitle         ( "Selecione o Tipo" );

//Caso alteração
$obLblTipoNorma = new Label;
$obLblTipoNorma->setRotulo( "Tipo" );
$obLblTipoNorma->setName  ( "stNomeTipoNorma" );
$obLblTipoNorma->setValue ( $stNomeTipoNorma );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo    ( "Número da Norma" );
$obTxtNorma->setTitle     ( "Informe o número da norma" );
$obTxtNorma->setName      ( "inNumNorma" );
$obTxtNorma->setValue     ( $inNumNorma  );
$obTxtNorma->setSize      ( 6 );
$obTxtNorma->setMaxLength ( 6 );
$obTxtNorma->setNull      ( false );
$obTxtNorma->obEvento->setOnChange("formataValoresNorma(this);buscaValor('MontaAtributos');");

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo            ( "Exercício" );
$obTxtExercicio->setTitle             ( "Informe o exercício da norma" );
$obTxtExercicio->setName              ( "stExercicio" );
$obTxtExercicio->setValue             ( $stExercicio  );
$obTxtExercicio->setSize              ( 6 );
$obTxtExercicio->setMaxLength         ( 4 );
$obTxtExercicio->setInteiro           ( true  );
$obTxtExercicio->setNull              ( false );
$obTxtExercicio->obEvento->setOnChange("validaExercicio(this)");

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName ( "inCodNorma" );
$obHdnCodNorma->setId   ( "inCodNorma" );
$obHdnCodNorma->setValue( $inCodNorma  );

$obHdnCodTipoNorma = new Hidden;
$obHdnCodTipoNorma->setName ( "inCodTipoNorma" );
$obHdnCodTipoNorma->setId   ( "inCodTipoNorma" );
$obHdnCodTipoNorma->setValue( $inCodTipoNorma  );

$obHdnCodLeiAlteracao = new Hidden;
$obHdnCodLeiAlteracao->setName ( "hdnInLeiAlteracao" );
$obHdnCodLeiAlteracao->setId   ( "hdnInLeiAlteracao" );
$obHdnCodLeiAlteracao->setValue(" ");

$obTxtNome = new TextBox;
$obTxtNome->setRotulo        ( "Nome" );
$obTxtNome->setName          ( "stNomeNorma" );
$obTxtNome->setValue         ( $stNomeNorma  );
$obTxtNome->setSize          ( 80 );
$obTxtNome->setMaxLength     ( 80 );
$obTxtNome->setNull          ( false );
$obTxtNome->setTitle         ( "Informe o nome da norma" );

$obTxtDescricao = new TextArea;
$obTxtDescricao->setRotulo   ( "Descrição" );
$obTxtDescricao->setName     ( "stDescricao" );
$obTxtDescricao->setValue    ( $stDescricao  );
$obTxtDescricao->setTitle    ( "Informe a descrição da norma" );

$obTxtData = new Data;
$obTxtData->setRotulo        ( "Data de Publicação" );
$obTxtData->setName          ( "stDataPublicacao" );
$obTxtData->setValue         ( $stDataPublicacao  );
$obTxtData->setNull          ( false );
$obTxtData->setTitle         ( "Informe a data de publicação da norma" );

$obTxtDataAssinatura = new Data;
$obTxtDataAssinatura->setRotulo        ( "Data de Assinatura" );
$obTxtDataAssinatura->setName          ( "stDataAssinatura" );
$obTxtDataAssinatura->setValue         ( $stDataAssinatura  );
$obTxtDataAssinatura->setNull          ( false );
$obTxtDataAssinatura->setTitle         ( "Informe a data de assinatura da norma" );

$obTxtDataTermino = new Data;
$obTxtDataTermino->setRotulo        ( "Data de Término" );
$obTxtDataTermino->setName          ( "stDataTermino" );
$obTxtDataTermino->setValue         ( $stDataTermino  );
$obTxtDataTermino->setTitle         ( "Informe a data de Término da norma" );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    case 02: //TCEAL
        include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaLei.class.php"           );
        include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDetalheAl.class.php"  );
        
        $obTNorma = new TNorma;
        $obTNormaDetalhe = new TNormaDetalheAl;
        
        $obTNormaDetalhe->setDado( 'cod_norma' , $_REQUEST['inCodNorma'] );
        $obTNormaDetalhe->recuperaPorChave($rsNormaDetalhe);
        
        if( $rsNormaDetalhe->getNumLinhas() > 0 ){
            $codLeiAlteracao = $rsNormaDetalhe->getCampo('cod_lei_alteracao');
            
            $obTNorma->setDado('cod_norma' , $rsNormaDetalhe->getCampo('cod_norma'));
            $obTNorma->recuperaPorChave($rsNormaAlteracao);
            
            $stNumNorma                = $rsNormaAlteracao->getCampo('num_norma');
            $stExercicioNormaAlteracao = $rsNormaAlteracao->getCampo('exercicio');
            $stNomeNormaAlteracao      = $rsNormaAlteracao->getCampo('nom_norma');
            $inCodTipoNormaAlteracao   = $rsNormaAlteracao->getCampo('cod_tipo_norma');
            
            Sessao::write( 'inCodNormaAlteracao' , $rsNormaAlteracao->getCampo('cod_norma') );
            Sessao::write( 'stDescNormaAlteracao', $rsNormaAlteracao->getCampo('descricao') );
        }
        
        $obNormaLei = new TNormaLei;
        $obNormaLei->recuperaTodos($rsNormaLei);
        
        $obCmbTipoAlteracao = new Select;
        $obCmbTipoAlteracao->setRotulo            ( "Alteração de Lei"             );
        $obCmbTipoAlteracao->setName              ( "stTipoLeiAlteracao"           );
        $obCmbTipoAlteracao->setId                ( "stTipoLeiAlteracao"           );
        $obCmbTipoAlteracao->setStyle             ( "width: 200px"                 );
        $obCmbTipoAlteracao->setCampoID           ( "cod_lei"                      );
        $obCmbTipoAlteracao->setCampoDesc         ( "[cod_lei] - [descricao]"      );
        $obCmbTipoAlteracao->addOption            ( "" , "Selecione"               );
        $obCmbTipoAlteracao->preencheCombo        ( $rsNormaLei                    );
        
        if ( $stAcao == "alterar" ){
            $obCmbTipoAlteracao->setValue         ( $codLeiAlteracao               );
        }
        
        $obCmbTipoAlteracao->setTitle             ( "Selecione o Tipo de Lei"      );
        $obCmbTipoAlteracao->obEvento->setOnChange("buscaValor('MontaBuscaNorma' );");
        
        if ( $codLeiAlteracao ){
            $obHdnCodNorma->setName ( "hdnstNormaAlteracao" );
            $obHdnCodNorma->setId   ( "hdnstNormaAlteracao" );
            $obHdnCodNorma->setValue( $stNumNorma."-".$stExercicioNormaAlteracao."-".$stNomeNormaAlteracao."-".$inCodTipoNormaAlteracao  );
            
            $obHdnCodLeiAlteracao->setName ( "hdnInLeiAlteracao" );
            $obHdnCodLeiAlteracao->setValue( $codLeiAlteracao );
        }
    break;
    case 27: //TCETO
        include_once ( CAM_GPC_TCETO_MAPEAMENTO."TTCETONormaDetalhe.class.php"           );
        include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaLei.class.php"           );
        
        $obTNorma = new TNorma;
        $obTTCETONormaDetalhe = new TTCETONormaDetalhe;
        
        $obTTCETONormaDetalhe->setDado( 'cod_norma' , $_REQUEST['inCodNorma'] );
        $obTTCETONormaDetalhe->recuperaPorChave($rsNormaDetalhe);
                
        if( $rsNormaDetalhe->getNumLinhas() > 0 ){
            $codLeiAlteracao = $rsNormaDetalhe->getCampo('cod_lei_alteracao');
                        
            $obTNorma->setDado('cod_norma' , $rsNormaDetalhe->getCampo('cod_norma'));
            $obTNorma->recuperaPorChave($rsNormaAlteracao);
            
            $stNumNorma                    = $rsNormaAlteracao->getCampo('num_norma');
            $stExercicioNormaAlteracao     = $rsNormaAlteracao->getCampo('exercicio');
            $stNomeNormaAlteracao          = $rsNormaAlteracao->getCampo('nom_norma');
            $inCodTipoNormaAlteracao       = $rsNormaAlteracao->getCampo('cod_tipo_norma');
            $numPercentualCreditoAdicional = $rsNormaDetalhe->getCampo('percentual_credito_adicional');
            
            Sessao::write( 'inCodNormaAlteracao' , $rsNormaAlteracao->getCampo('cod_norma') );
            Sessao::write( 'stDescNormaAlteracao', $rsNormaAlteracao->getCampo('descricao') );
            Sessao::write( 'numPercentualCreditoAdicional', $numPercentualCreditoAdicional );
        }
        
        $obNormaLei = new TNormaLei;
        $obNormaLei->recuperaTodos($rsNormaLei);
        
        $obCmbTipoAlteracao = new Select;
        $obCmbTipoAlteracao->setRotulo            ( "Alteração de Lei"             );
        $obCmbTipoAlteracao->setName              ( "stTipoLeiAlteracao"           );
        $obCmbTipoAlteracao->setId                ( "stTipoLeiAlteracao"           );
        $obCmbTipoAlteracao->setStyle             ( "width: 200px"                 );
        $obCmbTipoAlteracao->setCampoID           ( "cod_lei"                      );
        $obCmbTipoAlteracao->setCampoDesc         ( "[cod_lei] - [descricao]"      );
        $obCmbTipoAlteracao->addOption            ( "" , "Selecione"               );
        $obCmbTipoAlteracao->preencheCombo        ( $rsNormaLei                    );
        
        if ( $stAcao == "alterar" ){
            $obCmbTipoAlteracao->setValue         ( $codLeiAlteracao               );
        }
        
        $obCmbTipoAlteracao->setTitle             ( "Selecione o Tipo de Lei"      );
        $obCmbTipoAlteracao->obEvento->setOnChange("buscaValor('MontaBuscaNorma' );");
         
        if ( $codLeiAlteracao ){
            $obHdnCodNorma->setName ( "hdnstNormaAlteracao" );
            $obHdnCodNorma->setId   ( "hdnstNormaAlteracao" );
            $obHdnCodNorma->setValue( $stNumNorma."-".$stExercicioNormaAlteracao."-".$stNomeNormaAlteracao."-".$inCodTipoNormaAlteracao  );
            
            $obHdnCodLeiAlteracao->setName ( "hdnInLeiAlteracao" );
            $obHdnCodLeiAlteracao->setValue( $codLeiAlteracao );
        }
    break;
}
    
$obBtnLink = new FileBox;
$obBtnLink->setNull   ( true                           );
$obBtnLink->setRotulo ( "Arquivo"                      );
$obBtnLink->setTitle  ( "Informe o caminho do arquivo" );
$obBtnLink->setName   ( "btnIncluirLink"               );
$obBtnLink->setId     ( "btnIncluirLink"               );
$obBtnLink->setSize   ( 35                             );
$obBtnLink->setValue ( $btnIncluirLink  );

$obSpan = new Span;
$obSpan->setId ( "spanAtributos" );

$obSpanNorma = new Span;
$obSpanNorma->setId('spanNormas');

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );
$obForm->setEncType                 ( "multipart/form-data" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( "UC-01.04.02" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnEval , true);

$obFormulario->addTitulo            ( "Dados da Norma" );
if ($stAcao=='incluir') {
    $obFormulario->addComponenteComposto( $obTxtTipoNorma , $obCmbTipoNorma );
} else {
    $obFormulario->addHidden        ( $obHdnCodNorma        ); 
    $obFormulario->addHidden        ( $obHdnCodLeiAlteracao ); 
    $obFormulario->addHidden        ( $obHdnCodTipoNorma    );
    $obFormulario->addComponente    ( $obLblTipoNorma       );

}
$obFormulario->addComponente        ( $obTxtNorma         );
$obFormulario->addComponente        ( $obTxtExercicio     );
$obFormulario->addComponente        ( $obTxtNome          );
$obFormulario->addComponente        ( $obTxtDescricao     );
$obFormulario->addComponente        ( $obTxtData          );
$obFormulario->addComponente        ( $obTxtDataAssinatura);
$obFormulario->addComponente        ( $obTxtDataTermino   );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    case 02: //TCEAL
        $obFormulario->addComponente    ( $obCmbTipoAlteracao );
        $obFormulario->addSpan          ( $obSpanNorma        );
    break;
    case 27: //TCETO
        $obFormulario->addComponente    ( $obCmbTipoAlteracao );
        $obFormulario->addSpan          ( $obSpanNorma        );
    break;
}

$obFormulario->addComponente        ( $obBtnLink          );

if ($stAcao=='incluir') {
    $obFormulario->addSpan          ( $obSpan );
    $obFormulario->OK               ();
} else {
    $obMontaAtributos->geraFormulario( $obFormulario );
    $obFormulario->Cancelar();
}

$obFormulario->show                 ();

include_once($pgJs);

if ( $stAcao == "incluir" )
    $js .= "focusIncluir();";
else
    $js .= "focusAlterar();";

if ( $stAcao == "alterar" and $codLeiAlteracao > 0 ) {
    $js .= "buscaValor('MontaBuscaNorma');";
}
sistemaLegado::executaFrameOculto($js);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
