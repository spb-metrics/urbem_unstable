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
    * Data de Criação: 22/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/07/22 20:21:25  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
  *
  * Data de Criação: 22/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAMovimentoContabil extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAMovimentoContabil()
{
    parent::TContabilidadePlanoConta();

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
    $stSql .= " SELECT   exercicio                                                              \n";
    $stSql .= "         ,cod_estrutural                                                         \n";
    $stSql .= "         ,tipo_mov                                                               \n";
    $stSql .= "         ,mes                                                                    \n";
    $stSql .= "         ,abs(sum(valor_credito)) as vl_credito                                  \n";
    $stSql .= "         ,abs(sum(valor_debito))  as vl_debito                                   \n";
    $stSql .= " FROM (                                                                          \n";
    $stSql .= "     SELECT   pc.exercicio                                                       \n";
    $stSql .= "             ,pc.cod_estrutural                                                  \n";
    $stSql .= "             ,case when vl.tipo='I' then 1 else 3 end as tipo_mov                \n";
    $stSql .= "             ,to_char(lo.dt_lote,'mm') as mes                                    \n";
    $stSql .= "             ,sum(vl.vl_lancamento) as valor_credito                             \n";
    $stSql .= "             ,0.00 as valor_debito                                               \n";
    $stSql .= "     FROM     contabilidade.plano_conta      as pc                               \n";
    $stSql .= "             ,contabilidade.plano_analitica  as pa                               \n";
    $stSql .= "             ,contabilidade.conta_credito    as co                               \n";
    $stSql .= "             ,contabilidade.valor_lancamento as vl                               \n";
    $stSql .= "             ,contabilidade.lote             as lo                               \n";
    $stSql .= "     WHERE   pc.exercicio    = pa.exercicio                                      \n";
    $stSql .= "     AND     pc.cod_conta    = pa.cod_conta                                      \n";
    $stSql .= "     AND     pa.exercicio    = co.exercicio                                      \n";
    $stSql .= "     AND     pa.cod_plano    = co.cod_plano                                      \n";
    $stSql .= "     AND     co.exercicio    = vl.exercicio                                      \n";
    $stSql .= "     AND     co.cod_entidade = vl.cod_entidade                                   \n";
    $stSql .= "     AND     co.cod_lote     = vl.cod_lote                                       \n";
    $stSql .= "     AND     co.tipo         = vl.tipo                                           \n";
    $stSql .= "     AND     co.tipo_valor   = vl.tipo_valor                                     \n";
    $stSql .= "     AND     co.sequencia    = vl.sequencia                                      \n";
    $stSql .= "     AND     vl.exercicio    = lo.exercicio                                      \n";
    $stSql .= "     AND     vl.cod_entidade = lo.cod_entidade                                   \n";
    $stSql .= "     AND     vl.tipo         = lo.tipo                                           \n";
    $stSql .= "     AND     vl.cod_lote     = lo.cod_lote                                       \n";
    $stSql .= "     AND     pc.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     vl.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= "     GROUP BY pc.exercicio, pc.cod_estrutural, vl.tipo, to_char(lo.dt_lote,'mm') \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "     UNION                                                                       \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "     SELECT   pc.exercicio                                                       \n";
    $stSql .= "             ,pc.cod_estrutural                                                  \n";
    $stSql .= "             ,case when vl.tipo='I' then 1 else 3 end as tipo_mov                \n";
    $stSql .= "             ,to_char(lo.dt_lote,'mm') as mes                                    \n";
    $stSql .= "             ,0.00 as valor_credito                                              \n";
    $stSql .= "             ,sum(vl.vl_lancamento) as valor_debito                              \n";
    $stSql .= "     FROM     contabilidade.plano_conta      as pc                               \n";
    $stSql .= "             ,contabilidade.plano_analitica  as pa                               \n";
    $stSql .= "             ,contabilidade.conta_debito     as co                               \n";
    $stSql .= "             ,contabilidade.valor_lancamento as vl                               \n";
    $stSql .= "             ,contabilidade.lote             as lo                               \n";
    $stSql .= "     WHERE   pc.exercicio    = pa.exercicio                                      \n";
    $stSql .= "     AND     pc.cod_conta    = pa.cod_conta                                      \n";
    $stSql .= "     AND     pa.exercicio    = co.exercicio                                      \n";
    $stSql .= "     AND     pa.cod_plano    = co.cod_plano                                      \n";
    $stSql .= "     AND     co.exercicio    = vl.exercicio                                      \n";
    $stSql .= "     AND     co.cod_entidade = vl.cod_entidade                                   \n";
    $stSql .= "     AND     co.cod_lote     = vl.cod_lote                                       \n";
    $stSql .= "     AND     co.tipo         = vl.tipo                                           \n";
    $stSql .= "     AND     co.tipo_valor   = vl.tipo_valor                                     \n";
    $stSql .= "     AND     co.sequencia    = vl.sequencia                                      \n";
    $stSql .= "     AND     vl.exercicio    = lo.exercicio                                      \n";
    $stSql .= "     AND     vl.cod_entidade = lo.cod_entidade                                   \n";
    $stSql .= "     AND     vl.tipo         = lo.tipo                                           \n";
    $stSql .= "     AND     vl.cod_lote     = lo.cod_lote                                       \n";
    $stSql .= "     AND     pc.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     vl.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= "                                                                                 \n";
    $stSql .= "     GROUP BY pc.exercicio, pc.cod_estrutural, vl.tipo, to_char(lo.dt_lote,'mm') \n";
    $stSql .= " ) as tabela                                                                     \n";
    $stSql .= " GROUP BY exercicio, cod_estrutural, tipo_mov, mes                               \n";
    $stSql .= " ORDER BY exercicio, cod_estrutural, tipo_mov, mes                               \n";

    return $stSql;
}

}
