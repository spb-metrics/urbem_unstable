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
    * Data de Criação: 09/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26063 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-11 18:31:04 -0300 (Qui, 11 Out 2007) $

    * Casos de uso : uc-03.05.22
*/

/*
$Log$
Revision 1.1  2007/10/11 21:31:04  girardi
adicionando ao repositório (rescisão de contrato e aditivos de contrato)

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoContrato.class.php" );
include_once( TLIC."TLicitacaoRescisaoContrato.class.php" );
include_once( TLIC."TLicitacaoRescisaoContratoResponsavelJuridico.class.php" );
include_once( TLIC."TLicitacaoPublicacaoRescisaoContrato.class.php");

Sessao::getExercicio();
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$stMensagem = "";

switch ($stAcao) {

case "rescindir":
    $obTLicitacaoContrato = new TLicitacaoContrato();
    $obTLicitacaoContrato->setDado('exercicio_contrato', $_REQUEST['stExercicio']);
    $obTLicitacaoContrato->setDado('num_contrato', $_REQUEST['inNumContrato']);
    $obTLicitacaoContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTLicitacaoContrato->recuperaPorChave( $rsLicitacaoContrato );

    if ( implode(array_reverse(explode('/',$_REQUEST['dtRescisao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) ) {
        SistemaLegado::exibeAviso(urlencode("A data de rescisão não pode ser anterior que a data de assinatura do contrato"), "n_incluir", "erro" );

    } else {

        $obTLicitacaoRescisaoContrato = new TLicitacaoRescisaoContrato();
        $obTLicitacaoRescisaoContrato->recuperaProximoNumRescisao($rsLicitacaoRescisaoContrato);
        $obTLicitacaoRescisaoContrato->setDado('exercicio_contrato', $_REQUEST['stExercicio']);
        $obTLicitacaoRescisaoContrato->setDado('num_contrato', $_REQUEST['inNumContrato']);
        $obTLicitacaoRescisaoContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTLicitacaoRescisaoContrato->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoRescisaoContrato->setDado('num_rescisao', $rsLicitacaoRescisaoContrato->getCampo("maximo"));
        $obTLicitacaoRescisaoContrato->setDado('dt_rescisao', $_REQUEST['dtRescisao']);
        $vlMulta = number_format(str_replace(".", "", $_REQUEST['vlMulta']), 2, ".", "");
        $obTLicitacaoRescisaoContrato->setDado('vlr_multa', $vlMulta);
        $vlIndenizacao = number_format(str_replace(".", "", $_REQUEST['vlIndenizacao']), 2, ".", "");
        $obTLicitacaoRescisaoContrato->setDado('vlr_indenizacao', $vlIndenizacao);
        $obTLicitacaoRescisaoContrato->setDado('motivo', $_REQUEST['stMotivo']);
        $obTLicitacaoRescisaoContrato->inclusao();

        if ($_REQUEST['inCodResponsavelJuridico']) {
            $obTLicitacaoRescisContrRespJuridico = new TLicitacaoRescisaoContratoResponsavelJuridico();
            $obTLicitacaoRescisContrRespJuridico->setDado('exercicio_contrato', $_REQUEST['stExercicio']);
            $obTLicitacaoRescisContrRespJuridico->setDado('num_contrato', $_REQUEST['inNumContrato']);
            $obTLicitacaoRescisContrRespJuridico->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTLicitacaoRescisContrRespJuridico->setDado('numcgm', $_REQUEST['inCodResponsavelJuridico']);
            $obTLicitacaoRescisContrRespJuridico->inclusao();
        }
        
        //inclui os dados da publicacao do contrato
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arTemp) {
            $obTPublicacaoRescisaoContrato = new TLicitacaoPublicacaoRescisaoContrato;
            $obTPublicacaoRescisaoContrato->setDado( 'num_contrato', $request->get('inNumContrato') );
            $obTPublicacaoRescisaoContrato->setDado( 'cgm_imprensa', $arTemp['inVeiculo'] );
            $obTPublicacaoRescisaoContrato->setDado( 'dt_publicacao', $arTemp['dtDataPublicacao'] );
            $obTPublicacaoRescisaoContrato->setDado( 'num_publicacao',$arTemp['inNumPublicacao'] );
            $obTPublicacaoRescisaoContrato->setDado( 'exercicio_contrato', $request->get('stExercicio') );
            $obTPublicacaoRescisaoContrato->setDado( 'cod_entidade', $request->get('inCodEntidade') );
            $obTPublicacaoRescisaoContrato->setDado( 'observacao', $arTemp['stObservacao'] );
            $obTPublicacaoRescisaoContrato->inclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicio'],"incluir", "aviso", Sessao::getId(),"");
    }

    break;
}
Sessao::encerraExcecao();
