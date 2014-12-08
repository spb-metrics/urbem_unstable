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
  * Oculto de Relatório de Concessão de Vale-Tranporte
  * Data de Criação: 07/11/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * Casos de uso: uc-03.04.05, uc-03.03.05, uc-03.03.06

  $Id: OCIMontaSolicitacao.php 59612 2014-09-02 12:00:51Z gelson $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST["stCtrl"]) {
    case "preencheEntidade":

       $stJs .= "limpaSelect(f.stNomEntidadeSolicitacao,0); \n";
       $stJs .= " d.getElementById('inCodEntidadeSolicitacao').value = '';";
       if ($_REQUEST['inExercicio']) {
          include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
          $obTOrcamentoEntidade = new TOrcamentoEntidade;
          $obTOrcamentoEntidade->setDado('exercicio', $_REQUEST['inExercicio']);
          $obTOrcamentoEntidade->recuperaEntidadeGeral($rsRecord);
          $inCount = 1;

            if ($rsRecord->getNumLinhas()>1) {
                $stJs .= "f.stNomEntidadeSolicitacao.options[0] = new Option('Selecione','', 'selected');\n";
            } elseif ($rsRecord->getNumLinhas()==1) {
                $inCount--;
                $stJs .= "f.inCodEntidadeSolicitacao.value= ".$rsRecord->getCampo('cod_entidade').";\n";
            }
            while (!$rsRecord->eof()) {
                $inId   = $rsRecord->getCampo("cod_entidade");
                $stDesc = $rsRecord->getCampo("nom_cgm");

                $stJs .= "f.stNomEntidadeSolicitacao.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsRecord->proximo();
                $inCount++;
            }

       }
    break;

    case "limpaSolicitacao":
       $stJs .= " d.getElementById('inCodSolicitacao').value = '';";
       $stJs .= " d.getElementById('stNomSolicitacao').innerHTML = '&nbsp;';";
    break;

    case "verificaFiltro":
       $stJs .= "if ((d.getElementById('".$_GET['idEntidade']."').value == '') || (d.getElementById('".$_GET['idExercicio']."').value == '')) { \n
                     if (d.getElementById('imgSolicitacao')) {
                        d.getElementById('imgSolicitacao').style.display = 'none';\n
                        d.getElementById('inCodSolicitacao').disabled = true;\n
                     }
                   } else {\n
                     if (d.getElementById('imgSolicitacao')) {
                        d.getElementById('imgSolicitacao').style.display = 'inline';\n
                        d.getElementById('inCodSolicitacao').disabled = false;\n
                     }
                   }; \n";
    break;

    # TIPOS DE BUSCA

    case "solicitacao":

        if ($_GET['stExercicio']) {
            if ($_GET['inCodEntidade']) {
                if ($_GET['inCodSolicitacao']) {
                    include_once( CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php" );
                    $obTComprasSolicitacao = new TComprasSolicitacao;
                    $obTComprasSolicitacao->setDado('exercicio', $_GET['stExercicio'] );
                    $obTComprasSolicitacao->setDado('cod_entidade' , $_GET['inCodEntidade'] );
                    $obTComprasSolicitacao->setDado('cod_solicitacao' , $_GET['inCodSolicitacao'] );
                    if ($_REQUEST['inCodSolicitacaoExcluida']!="") {
                      $stFiltro.=" AND solicitacao.cod_solicitacao != ".$_GET['inCodSolicitacaoExcluida']." \n";
                    }
                    $obErro = $obTComprasSolicitacao->recuperaRelacionamentoSolicitacao( $rsRecordSet, $stFiltro );
                    if (!$obErro->ocorreu()) {
                        if ($rsRecordSet->getNumLinhas() > 0) {
                            $stDescricao = $rsRecordSet->getCampo('solicitante');
                            $stJs .= " d.getElementById('".$_GET['campoDesc']."').innerHTML = '".$stDescricao."'; ";
                        } else {
                            $stJs .= "alertaAviso('Código de solicitação inválido (".$_GET['inCodSolicitacao'].") para a entidade informada.','form','erro','".Sessao::getId()."');\n";
                            $stJs .= "d.getElementById('".$_GET['campoCod']."').value = ''; ";
                            $stJs .= "d.getElementById('".$_GET['campoDesc']."').innerHTML = '&nbsp;'; ";
                        }
                    }
                }
            } else {
                $stJs .= "alertaAviso('Selecione uma entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "d.getElementById('".$_GET['campoCod']."').value = ''; ";
                $stJs .= "d.getElementById('".$_GET['campoDesc']."').innerHTML = '&nbsp;'; ";

            }
        } else $stJs .= "alertaAviso('Informe um exercício.','form','erro','".Sessao::getId()."');\n";
    break;

    case "mapa_compras":

        $inCodSolicitacao = $_REQUEST['inCodSolicitacao'];
        $inCodEntidade    = $_REQUEST['inCodEntidade'];
        $stExercicio      = $_REQUEST['stExercicio'];
        $stMsgAviso       = "";

        if (is_numeric($stExercicio)) {
            if (is_numeric($inCodEntidade)) {
                if (is_numeric($inCodSolicitacao)) {

                    # Verifica se a Solicitação de Compras está Homologada e não possui uma anulação para essa homologação.
                    include CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoHomologada.class.php";
                    $obTComprasSolicitacaoHomologada = new TComprasSolicitacaoHomologada;
                    $obTComprasSolicitacaoHomologada->setDado('exercicio'       , $stExercicio);
                    $obTComprasSolicitacaoHomologada->setDado('cod_entidade'    , $inCodEntidade);
                    $obTComprasSolicitacaoHomologada->setDado('cod_solicitacao' , $inCodSolicitacao);
                    $obTComprasSolicitacaoHomologada->recuperaSolicitacaoHomologadaNaoAnulada($rsSolicitacaoHomologadaNaoAnulada);

                    if ($rsSolicitacaoHomologadaNaoAnulada->getNumLinhas() <= 0) {
                        $stMsgAviso = "Essa Solicitação (".$inCodSolicitacao.") pode não estar homologada ou então possuir uma anulação da homologação.";
                    }

                    # Verifica se a solicitação não foi anulada nas ações de Solicitação de Compra.
                    include CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItem.class.php";
                    $obTComprasSolicitacaoItem = new TComprasSolicitacaoItem;
                    $obTComprasSolicitacaoItem->setDado('cod_solicitacao' , $inCodSolicitacao);
                    $obTComprasSolicitacaoItem->setDado('cod_entidade'    , $inCodEntidade);
                    $obTComprasSolicitacaoItem->setDado('exercicio'       , $stExercicio);
                    $obTComprasSolicitacaoItem->recuperaItemConsultaSolicitacao($rsSolicitacaoCompras);

                    $inTotalDisponivel = 0;

                    while (!$rsSolicitacaoCompras->eof()) {
                        if ($rsSolicitacaoCompras->getCampo('quantidade') > $rsSolicitacaoCompras->getCampo('quantidade_anulada')) {
                            $inTotalDisponivel++;
                            $stDescricao = $rsSolicitacaoCompras->getCampo('nom_cgm');
                        }

                        $rsSolicitacaoCompras->proximo();
                    }

                    if ($inTotalDisponivel == 0) {
                        $stMsgAviso = "Essa Solicitação (".$inCodSolicitacao.") não possui saldo disponível para ser utilizada no Mapa de Compras.";
                    }

                    if (empty($stMsgAviso)) {
                        $stJs .= "jQuery('#".$_GET['campoDesc']."').html('".$stDescricao."'); \n";
                    } else {
                        $stJs .= "jQuery('#".$_GET['campoCod']."').val('');                           \n";
                        $stJs .= "jQuery('#".$_GET['campoDesc']."').html('&nbsp;');                   \n";
                        $stJs .= "alertaAviso('".$stMsgAviso."','form','erro','".Sessao::getId()."'); \n";
                    }
                }
            } else {
                $stJs .= "alertaAviso('Selecione uma entidade.','form','erro','".Sessao::getId()."');  \n";
                $stJs .= "jQuery('#".$_GET['campoCod']."').val('');                                     \n";
                $stJs .= "jQuery('#".$_GET['campoDesc']."').html('&nbsp;');                             \n";
            }
        } else {
            $stJs .= "alertaAviso('Informe o Exercício da Solicitação.','form','erro','".Sessao::getId()."'); \n";
        }

    break;
}

if (!empty($stJs)) {
    echo $stJs;
}
