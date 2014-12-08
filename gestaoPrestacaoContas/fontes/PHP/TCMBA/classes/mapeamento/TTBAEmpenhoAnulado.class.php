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
    * Data de Criação: 12/07/2007

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
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnulado.class.php" );

/**
  *
  * Data de Criação: 12/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAEmpenhoAnulado extends TEmpenhoEmpenhoAnulado
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAEmpenhoAnulado()
{
    parent::TEmpenhoEmpenhoAnulado();

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
    $stSql .= " SELECT   des.exercicio                                                      \n";
    $stSql .= "         ,des.num_orgao                                                      \n";
    $stSql .= "         ,des.num_unidade                                                    \n";
    $stSql .= "         ,emp.cod_empenho                                                    \n";
    $stSql .= "         ,substr(ean.oid,length(ean.oid)-6,7) as numero_empenho_anulado      \n";
    $stSql .= "         ,sume.valor_anulado                                                 \n";
    $stSql .= "         ,case when liq.cod_entidade is not null then 1 else  2 end as foi_liquidada  \n";
    $stSql .= "         ,to_char(ean.timestamp,'dd/mm/yyyy') as data_anulacao               \n";
    $stSql .= " FROM     empenho.empenho             as emp                                 \n";
    $stSql .= "          LEFT JOIN                                                          \n";
    $stSql .= "              ( SELECT  exercicio_empenho                                    \n";
    $stSql .= "                       ,cod_entidade                                         \n";
    $stSql .= "                       ,cod_empenho                                          \n";
    $stSql .= "               FROM    empenho.nota_liquidacao as liq                        \n";
    $stSql .= "               WHERE   exercicio = '".$this->getDado('exercicio')."'         \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "             AND   cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= "               GROUP BY exercicio_empenho, cod_entidade, cod_empenho         \n";
    $stSql .= "               ) as liq                                                      \n";
    $stSql .= "             ON (                                                            \n";
    $stSql .= "                   emp.exercicio    = liq.exercicio_empenho                  \n";
    $stSql .= "               AND emp.cod_entidade = liq.cod_entidade                       \n";
    $stSql .= "               AND emp.cod_empenho  = liq.cod_empenho                        \n";
    $stSql .= "               )                                                             \n";
    $stSql .= "         ,empenho.empenho_anulado     as ean                                 \n";
    $stSql .= "         ,empenho.pre_empenho         as pre                                 \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as ped                                 \n";
    $stSql .= "         ,orcamento.conta_despesa     as cde                                 \n";
    $stSql .= "         ,orcamento.despesa           as des                                 \n";
    $stSql .= "         ,(                                                                  \n";
    $stSql .= "             SELECT   exercicio                                              \n";
    $stSql .= "                     ,cod_entidade                                           \n";
    $stSql .= "                     ,cod_empenho                                            \n";
    $stSql .= "                     ,timestamp                                              \n";
    $stSql .= "                     ,sum(vl_anulado) as valor_anulado                       \n";
    $stSql .= "             FROM    empenho.empenho_anulado_item as ipe                     \n";
    $stSql .= "             WHERE   to_char(timestamp,'yyyy') = '".$this->getDado('exercicio')."' \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "             AND   cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= "             GROUP BY exercicio, cod_entidade, cod_empenho, timestamp        \n";
    $stSql .= "         ) as sume                                                           \n";
    $stSql .= "                                                                             \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio                                 \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho                           \n";
    $stSql .= " AND     emp.exercicio       = ean.exercicio                                 \n";
    $stSql .= " AND     emp.cod_entidade    = ean.cod_entidade                              \n";
    $stSql .= " AND     emp.cod_empenho     = ean.cod_empenho                               \n";
    $stSql .= " AND     ean.exercicio       = sume.exercicio                                \n";
    $stSql .= " AND     ean.cod_entidade    = sume.cod_entidade                             \n";
    $stSql .= " AND     ean.cod_empenho     = sume.cod_empenho                              \n";
    $stSql .= " AND     ean.timestamp       = sume.timestamp                                \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio                                 \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho                           \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                                 \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa                               \n";
    $stSql .= " AND     ped.exercicio       = cde.exercicio                                 \n";
    $stSql .= " AND     ped.cod_conta       = cde.cod_conta                                 \n";
    $stSql .= " AND     des.exercicio       = '".$this->getDado('exercicio')."'             \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND   emp.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= " ORDER BY des.exercicio, emp.cod_empenho                                     \n";

    return $stSql;
}

}
