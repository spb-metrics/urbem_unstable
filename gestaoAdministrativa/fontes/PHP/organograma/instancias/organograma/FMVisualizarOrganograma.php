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
* Arquivo de instância para manutenção de organograma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 13754 $
$Name$
$Author: cassiano $
$Date: 2006-08-09 14:14:15 -0300 (Qua, 09 Ago 2006) $

Casos de uso: uc-01.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php");
include_once '../../../framework/legado/funcoesLegado.lib.php';

$stPrograma = "VisualizarOrganograma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->obEvento->setOnClick ( "voltar ();" );

 $sSQL = "SELECT cod_tipo_norma
               , nom_tipo_norma
               , cod_norma
               , nom_norma
               , CASE WHEN permissao_hierarquica = true THEN 'Sim' ELSE 'Não' END as permissao_hierarquica
            
            FROM organograma.organograma
       
       LEFT JOIN normas.norma
           USING (cod_norma)
       
       LEFT JOIN normas.tipo_norma
           USING (cod_tipo_norma)
           WHERE cod_organograma = ".$_REQUEST["inCodOrganograma"]." ";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $codTipoNorma      = trim($dbEmp->pegaCampo("cod_tipo_norma"));
    $nomTipoNorma      = trim($dbEmp->pegaCampo("nom_tipo_norma"));
    $codNorma          = trim($dbEmp->pegaCampo("cod_norma"));
    $nomNorma          = trim($dbEmp->pegaCampo("nom_norma"));
    $boPermissaoHierarquica = trim($dbEmp->pegaCampo("permissao_hierarquica"));
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

    $obCodNomTipoNorma = new Label();
    $obCodNomTipoNorma->setRotulo("Tipo Norma");
    $obCodNomTipoNorma->setValue($codTipoNorma." - ".$nomTipoNorma);

    $obCodNomNorma = new Label();
    $obCodNomNorma->setRotulo("Norma");
    $obCodNomNorma->setValue($codNorma." - ".$nomNorma);

    $obPermissaoHierarquica = new Label();
    $obPermissaoHierarquica->setRotulo("Permissão Hierárquica");
    $obPermissaoHierarquica->setValue($boPermissaoHierarquica);

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgFilt );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( 'UC-01.05.01' );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Visualização do Organograma" );
$obFormulario->addComponente ( $obCodNomTipoNorma );
$obFormulario->addComponente ( $obCodNomNorma );
$obFormulario->addComponente ( $obPermissaoHierarquica );

// RECORDSET E DEFINICAO DE REGRAS
$rsTipoNorma = new RecordSet;
$rsNorma     = new RecordSet;
$obRegra     = new ROrganogramaOrgao;
$arNiveisOrganograma = array ();
$obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
$obRegra->obROrganograma->consultar();
$stDataImplantacao = $obRegra->obROrganograma->getDtImplantacao();

$obErro = $obRegra->listaVisualizacaoOrganograma ($arNiveisOrganograma);

if ( $obErro->ocorreu () ) {
    sistemaLegado::exibeAviso (urlencode($obErro->getDescricao()),"","erro");
} else {
    $inNumArvores = count($arNiveisOrganograma);

    if ($inNumArvores <= 1) {

        if ($inNumArvores == 0) {
            $arNiveisOrganograma = array();
        } else {
            $arNiveisOrganograma = $arNiveisOrganograma[0];
        }

        $rsArvore = new RecordSet;
        $rsArvore->preenche ($arNiveisOrganograma);
        $obArvore = new Arvore;
        $obArvore->setRecordSet( $rsArvore );
        $obArvore->setName("orgao");
        $obArvore->setNameReduzido("orgao");
        $obArvore->setValue("[orgao] - [descricao] [situacao]");
        $obArvore->setRotulo( $stDataImplantacao );
        $obFormulario->addComponente    ( $obArvore );
    } else {
        for ($inCount = 0; $inCount < $inNumArvores; $inCount++) {
            $rsArvore = new RecordSet;
            $stNome = 'obArvore'.$inCount;

            $rsArvore->preenche ($arNiveisOrganograma[$inCount]);
            $$stNome = new Arvore;
            $$stNome->setRecordSet( $rsArvore );
            $$stNome->setName("orgao".$inCount);
            $$stNome->setNameReduzido("orgao");
            $$stNome->setValue("[orgao] - [descricao] [situacao]");
            $$stNome->setRotulo( $stDataImplantacao );
            $obFormulario->addComponente    ( $$stNome );
        }
    }
}

$obFormulario->defineBarra ( array( $obBtnVoltar ) ,'','');

$obFormulario->show();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
