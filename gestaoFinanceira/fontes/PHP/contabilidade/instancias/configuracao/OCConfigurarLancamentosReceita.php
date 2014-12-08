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
  * Formulário oculto
  * Data de criação : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Programador: Davi Aroldi

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoReceita.class.php" );
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

function montaLancamentoReceita()
{
  $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
  $stFiltro = " WHERE configuracao_lancamento_receita.cod_conta_receita = ".$_REQUEST['cod_conta_receita']."
                  AND configuracao_lancamento_receita.estorno = 'false'
                  AND configuracao_lancamento_receita.exercicio = '".Sessao::getExercicio()."' ";
  $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasConfiguracaoReceita( $rsRecordSet, $stFiltro );

  $stJs = "";
  $stJs .= " jQuery('#codContaReceitaLista').val(".$_REQUEST['cod_conta_receita']."); \n";

  if ($rsRecordSet->getNumLinhas() > 0) {
    while (!$rsRecordSet->eof()) {

      $stJs .= " jQuery('#".$rsRecordSet->getCampo('tipo_arrecadacao')."').attr('checked', true); \n";
      $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_receita'), $rsRecordSet->getCampo('tipo_arrecadacao'));
      $stJs .= " jQuery('#stLancamentoCreditoReceita').val(".$rsRecordSet->getCampo('cod_conta')."); \n";

      $rsRecordSet->proximo();
    }
  } else {
    $stJs .= " jQuery('#arrecadacaoDireta').attr('checked', true); \n";
    $stJs .= montaCombos($_REQUEST['cod_conta_receita'], 'arrecadacaoDireta');
    $stJs .= " jQuery('#stLancamentoCreditoReceita').val(''); \n";
  }

  return $stJs;
}

function montaCombos($inCodDespesa, $stValorRadio = "")
{
  $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
  $stOrdem = " ORDER BY pc.cod_estrutural ";
  $stJs = "";

  switch ($stValorRadio) {
    case "arrecadacaoDireta":
      $stFiltro = " AND pc.exercicio = '".Sessao::getExercicio()."'
            AND  ( pc.cod_estrutural like '4.%'
                /*OR pc.cod_estrutural like '4.1.2.%'
                OR pc.cod_estrutural like '4.1.3.%'
                OR pc.cod_estrutural like '4.2.0.%'
                OR pc.cod_estrutural like '4.2.1.%'
                OR pc.cod_estrutural like '4.2.2.%'
                OR pc.cod_estrutural like '4.2.3.%'
                OR pc.cod_estrutural like '4.2.4.%'
                OR pc.cod_estrutural like '4.3.0.%'
                OR pc.cod_estrutural like '4.3.1.%'
                OR pc.cod_estrutural like '4.3.2.%'
                OR pc.cod_estrutural like '4.3.3.%'
                OR pc.cod_estrutural like '4.4.0.%'
                OR pc.cod_estrutural like '4.4.1.%'
                OR pc.cod_estrutural like '4.4.2.%'
                OR pc.cod_estrutural like '4.4.4.%'
                OR pc.cod_estrutural like '4.4.5.%'
                OR pc.cod_estrutural like '4.4.9.%'*/ ) ";
      break;
    case "operacoesCredito":
      $stFiltro = " AND pc.exercicio = '".Sessao::getExercicio()."'
                    AND pc.cod_estrutural like '2.1.2.%' ";
      break;

    case "alienacaoBens":
      $stFiltro = " AND pc.exercicio = '".Sessao::getExercicio()."'
                    AND pc.cod_estrutural like '1.2.3.%' ";
      break;

    case "dividaAtiva":
      $stFiltro = " AND pc.exercicio = '".Sessao::getExercicio()."'
                    AND ( pc.cod_estrutural like '1.1.2.3.%' OR pc.cod_estrutural like '1.1.2.4.%' )";
      break;
  }

  $obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsCredito, $stFiltro, $stOrdem);

  $stJs .= "jQuery('#stLancamentoCreditoReceita').find('option').remove().end().append('<option value=\'\'>Selecione</option>'); \n";

  while (!$rsCredito->eof()) {
    $stJs .= "jQuery('#stLancamentoCreditoReceita').append('<option value=\'".$rsCredito->getCampo('cod_conta')."\'>".$rsCredito->getCampo('cod_estrutural')." - ".$rsCredito->getCampo('nom_conta')."</option>'); \n";
    $rsCredito->proximo();
  }

  return $stJs;
}

$stJs = '';
switch ($_REQUEST['stCtrl']) {
    case 'montaLancamentoReceita':
        $stJs .= montaLancamentoReceita();
        break;

    case 'carregaContasLancamento':
        $stJs .= montaCombos($_REQUEST['cod_conta_receita'], $_REQUEST['valor_radio']);
        break;

    default:
        # code...
        break;
}

echo ($stJs);
?>
