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
    * Data de Criação: 14/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.1  2007/07/16 02:39:13  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPaga.class.php" );

/**
  *
  * Data de Criação: 14/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPagamento extends TEmpenhoNotaLiquidacaoPaga
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPagamento()
{
    parent::TEmpenhoNotaLiquidacaoPaga();

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
    $stSql .= " SELECT   to_char(pag.timestamp,'yyyy') as exercicio                     \n";
    $stSql .= "         ,des.num_orgao                                                  \n";
    $stSql .= "         ,des.num_unidade                                                \n";
    $stSql .= "         ,emp.cod_empenho                                                \n";
    $stSql .= "         ,to_char(pag.timestamp,'dd/mm/yyyy') as data_pagamento          \n";
    $stSql .= "         ,sum(vl_pago)+sum(vl_anulado) as saldo                          \n";
    $stSql .= "         ,plc.cod_estrutural                                             \n";
    $stSql .= " FROM     empenho.empenho                as emp                          \n";
    $stSql .= "         ,empenho.nota_liquidacao        as liq                          \n";
    $stSql .= "         ,empenho.nota_liquidacao_paga   as pag                          \n";
    $stSql .= "         LEFT JOIN                                                       \n";
    $stSql .= "         (                                                               \n";
    $stSql .= "             SELECT   exercicio                                          \n";
    $stSql .= "                     ,cod_entidade                                       \n";
    $stSql .= "                     ,cod_nota                                           \n";
    $stSql .= "                     ,timestamp                                          \n";
    $stSql .= "                     ,sum(vl_anulado) as vl_anulado                      \n";
    $stSql .= "             FROM    empenho.nota_liquidacao_paga_anulada                \n";
    $stSql .= "             WHERE   to_char(timestamp_anulada,'yyyy') = '".$this->getDado('exercicio')."' \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "           AND   cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= "             GROUP BY exercicio, cod_entidade, cod_nota, timestamp       \n";
    $stSql .= "         ) as paa                                                        \n";
    $stSql .= "         ON (                                                            \n";
    $stSql .= "                 pag.exercicio   = paa.exercicio                         \n";
    $stSql .= "             AND pag.cod_entidade= paa.cod_entidade                      \n";
    $stSql .= "             AND pag.cod_nota    = paa.cod_nota                          \n";
    $stSql .= "             AND pag.timestamp   = paa.timestamp                         \n";
    $stSql .= "         )                                                               \n";
    $stSql .= "         LEFT JOIN                                                       \n";
    $stSql .= "          empenho.nota_liquidacao_conta_pagadora as lcp                  \n";
    $stSql .= "         ON (        pag.exercicio           = lcp.exercicio_liquidacao  \n";
    $stSql .= "             AND     pag.cod_entidade        = lcp.cod_entidade          \n";
    $stSql .= "             AND     pag.cod_nota            = lcp.cod_nota              \n";
    $stSql .= "             AND     pag.timestamp           = lcp.timestamp             \n";
    $stSql .= "         )                                                               \n";
    $stSql .= "         LEFT JOIN                                                       \n";
    $stSql .= "          contabilidade.plano_analitica as pla                           \n";
    $stSql .= "         ON (                                                            \n";
    $stSql .= "                 lcp.exercicio = pla.exercicio                           \n";
    $stSql .= "             AND lcp.cod_plano = pla.cod_plano                           \n";
    $stSql .= "         )                                                               \n";
    $stSql .= "         LEFT JOIN                                                       \n";
    $stSql .= "          contabilidade.plano_conta as plc                               \n";
    $stSql .= "         ON (                                                            \n";
    $stSql .= "                 pla.exercicio = plc.exercicio                           \n";
    $stSql .= "             AND pla.cod_conta = plc.cod_conta                           \n";
    $stSql .= "         )                                                               \n";
    $stSql .= "         ,empenho.pre_empenho            as pre                          \n";
    $stSql .= "         ,empenho.pre_empenho_despesa    as ped                          \n";
    $stSql .= "         ,orcamento.despesa              as des                          \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio                             \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho                       \n";
    $stSql .= " AND     emp.exercicio       = liq.exercicio_empenho                     \n";
    $stSql .= " AND     emp.cod_entidade    = liq.cod_entidade                          \n";
    $stSql .= " AND     emp.cod_empenho     = liq.cod_empenho                           \n";
    $stSql .= " AND     liq.exercicio       = pag.exercicio                             \n";
    $stSql .= " AND     liq.cod_entidade    = pag.cod_entidade                          \n";
    $stSql .= " AND     liq.cod_nota        = pag.cod_nota                              \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio                             \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho                       \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                             \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa                           \n";
    $stSql .= " AND     to_char(pag.timestamp,'yyyy')='".$this->getDado('exercicio')."' \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND   des.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= " AND     liq.cod_entidade in (1,2,3)                                     \n";
    $stSql .= " GROUP BY  to_char(pag.timestamp,'yyyy')                                 \n";
    $stSql .= "         ,des.num_orgao                                                  \n";
    $stSql .= "         ,des.num_unidade                                                \n";
    $stSql .= "         ,emp.cod_empenho                                                \n";
    $stSql .= "         ,to_char(pag.timestamp,'dd/mm/yyyy')                            \n";
    $stSql .= "         ,plc.cod_estrutural                                             \n";
    $stSql .= " ORDER BY  to_char(pag.timestamp,'yyyy')                                 \n";
    $stSql .= "         ,des.num_orgao                                                  \n";
    $stSql .= "         ,des.num_unidade                                                \n";
    $stSql .= "         ,emp.cod_empenho                                                \n";
    $stSql .= "         ,to_char(pag.timestamp,'dd/mm/yyyy')                            \n";
    $stSql .= "         ,plc.cod_estrutural                                             \n";

    return $stSql;
}

}
