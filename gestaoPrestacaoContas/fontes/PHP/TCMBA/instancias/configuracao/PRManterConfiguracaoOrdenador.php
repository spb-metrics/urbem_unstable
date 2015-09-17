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
    * 
    * Data de Criação   : 26/09/2014

    * @author Analista: Ane Caroline F.
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCMBA_MAPEAMENTO."TTCMBAConfiguracaoOrdenador.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrdenador";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($_REQUEST['stAcao']) {
  default:
     $obErro = new Erro();
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTTCMBAConfiguracaoOrdenador = new TTCMBAConfiguracaoOrdenador();

        if(!$obErro->ocorreu()){
            
          $arOrdenadores = Sessao::read('arOrdenadores');

          if (is_array($arOrdenadores) && count($arOrdenadores) > 0) {
             $obTTCMBAConfiguracaoOrdenador->setDado("exercicio",'2015');
             $obTTCMBAConfiguracaoOrdenador->setDado("cod_entidade",$request->get('hdnCodEntidade'));
             $obTTCMBAConfiguracaoOrdenador->setDado("num_orgao",$_REQUEST['inMontaCodOrgaoM']);
             $obTTCMBAConfiguracaoOrdenador->setDado("num_unidade",$_REQUEST['inMontaCodUnidadeM']);
             $obErro = $obTTCMBAConfiguracaoOrdenador->exclusao($boTransacao);
                 
             foreach( $arOrdenadores AS $arOrdenador ){
                $obTTCMBAConfiguracaoOrdenador->setDado("exercicio"              ,Sessao::getExercicio());
                $obTTCMBAConfiguracaoOrdenador->setDado("cod_entidade"           ,$arOrdenador['cod_entidade']);
                $obTTCMBAConfiguracaoOrdenador->setDado("cgm_ordenador"          ,$arOrdenador['cgm_ordenador']);
                $obTTCMBAConfiguracaoOrdenador->setDado("num_unidade"            ,$_REQUEST['inMontaCodUnidadeM']);
                $obTTCMBAConfiguracaoOrdenador->setDado("num_orgao"              ,$_REQUEST['inMontaCodOrgaoM']);
                $obTTCMBAConfiguracaoOrdenador->setDado("dt_inicio_vigencia"     ,$arOrdenador['dt_inicio_vigencia']);
                $obTTCMBAConfiguracaoOrdenador->setDado("dt_fim_vigencia"        ,$arOrdenador['dt_fim_vigencia']);
                $obTTCMBAConfiguracaoOrdenador->setDado("cod_tipo_responsavel"   ,$arOrdenador['cod_tipo_responsavel']);
                $obTTCMBAConfiguracaoOrdenador->recuperaPorChave($rsRecordSet, $boTransacao);
                
                if ($rsRecordSet->eof()) {
                    $obErro = $obTTCMBAConfiguracaoOrdenador->inclusao($boTransacao);
                } else {
                    $obErro = $obTTCMBAConfiguracaoOrdenador->alteracao($boTransacao);
                }
            }

          } else {
                $obTTCMBAConfiguracaoOrdenador->setDado("exercicio",Sessao::getExercicio());
                $obTTCMBAConfiguracaoOrdenador->setDado("cod_entidade",$request->get('hdnCodEntidade'));
                $obTTCMBAConfiguracaoOrdenador->setDado("num_orgao",$_REQUEST['inMontaCodOrgaoM']);
                $obTTCMBAConfiguracaoOrdenador->setDado("num_unidade",$_REQUEST['inMontaCodUnidadeM']);
                $obErro = $obTTCMBAConfiguracaoOrdenador->exclusao($boTransacao);
            }
            if(!$obErro->ocorreu()){
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEPEConfiguracaoOrdenador);
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
            }else{
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    
  break;
}

?>
