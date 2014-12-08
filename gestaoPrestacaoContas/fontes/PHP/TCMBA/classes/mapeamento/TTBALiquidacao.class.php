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
    * Extensão da Classe de mapeamento
    * Data de Criação: 13/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/07/16 02:39:13  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php" );

/**
  *
  * Data de Criação: 13/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBALiquidacao extends TEmpenhoNotaLiquidacao
{
/**
    * Método Construtor
    * @access Private
*/
function TTBALiquidacao()
{
    parent::TEmpenhoNotaLiquidacao();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT   liq.exercicio                                      \n";
    $stSql .= "         ,des.num_orgao                                      \n";
    $stSql .= "         ,des.num_unidade                                    \n";
    $stSql .= "         ,emp.cod_empenho                                    \n";
    $stSql .= "         ,to_char(liq.dt_liquidacao,'dd/mm/yyyy') as data_liquidacao \n";
    $stSql .= "         ,(sum(sum.valor)-sum(sumanu.valor) ) as saldo       \n";
    $stSql .= " FROM     empenho.empenho             as emp                 \n";
    $stSql .= "         ,empenho.nota_liquidacao     as liq                 \n";
    $stSql .= "         LEFT JOIN                                           \n";
    $stSql .= "         (                                                   \n";
    $stSql .= "             SELECT   exercicio                              \n";
    $stSql .= "                     ,cod_entidade                           \n";
    $stSql .= "                     ,cod_nota                               \n";
    $stSql .= "                     ,sum(vl_anulado) as valor               \n";
    $stSql .= "             FROM    empenho.nota_liquidacao_item_anulado as nli        \n";
    $stSql .= "             WHERE   exercicio = '".$this->getDado('exercicio')."'         \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "           AND   cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= "             GROUP BY exercicio, cod_entidade, cod_nota      \n";
    $stSql .= "         ) as sumanu                                         \n";
    $stSql .= "         ON (                                                \n";
    $stSql .= "             liq.exercicio    = sumanu.exercicio             \n";
    $stSql .= "         AND liq.cod_entidade = sumanu.cod_entidade          \n";
    $stSql .= "         AND liq.cod_nota     = sumanu.cod_nota              \n";
    $stSql .= "         )                                                   \n";
    $stSql .= "         ,empenho.pre_empenho         as pre                 \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as ped                 \n";
    $stSql .= "         ,orcamento.conta_despesa     as cde                 \n";
    $stSql .= "         ,orcamento.despesa           as des                 \n";
    $stSql .= "         ,(                                                  \n";
    $stSql .= "             SELECT   exercicio                              \n";
    $stSql .= "                     ,cod_entidade                           \n";
    $stSql .= "                     ,cod_nota                               \n";
    $stSql .= "                     ,sum(vl_total) as valor                 \n";
    $stSql .= "             FROM    empenho.nota_liquidacao_item as nli     \n";
    $stSql .= "             WHERE   exercicio = '".$this->getDado('exercicio')."'         \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "           AND   cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= "             GROUP BY exercicio, cod_entidade, cod_nota      \n";
    $stSql .= "         ) as sum                                            \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio                 \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho           \n";
    $stSql .= " AND     emp.exercicio       = liq.exercicio_empenho         \n";
    $stSql .= " AND     emp.cod_entidade    = liq.cod_entidade              \n";
    $stSql .= " AND     emp.cod_empenho     = liq.cod_empenho               \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio                 \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho           \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                 \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa               \n";
    $stSql .= " AND     ped.exercicio       = cde.exercicio                 \n";
    $stSql .= " AND     ped.cod_conta       = cde.cod_conta                 \n";
    $stSql .= " AND     liq.exercicio       = sum.exercicio                 \n";
    $stSql .= " AND     liq.cod_entidade    = sum.cod_entidade              \n";
    $stSql .= " AND     liq.cod_nota        = sum.cod_nota                  \n";
    $stSql .= " AND     liq.exercicio       = '".$this->getDado('exercicio')."'             \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND   liq.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= " GROUP BY  liq.exercicio                                     \n";
    $stSql .= "         ,des.num_orgao                                      \n";
    $stSql .= "         ,des.num_unidade                                    \n";
    $stSql .= "         ,emp.cod_empenho                                    \n";
    $stSql .= "         ,liq.dt_liquidacao                                  \n";
    $stSql .= " ORDER BY  liq.exercicio                                     \n";
    $stSql .= "         ,des.num_orgao                                      \n";
    $stSql .= "         ,des.num_unidade                                    \n";
    $stSql .= "         ,emp.cod_empenho                                    \n";
    $stSql .= "         ,liq.dt_liquidacao                                  \n";

    return $stSql;
}

}
