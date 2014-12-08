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
    * Data de Criação: 24/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.3  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/07/25 02:30:19  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );

/**
  *
  * Data de Criação: 24/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAReceitaArrecadada extends TContabilidadeLancamentoReceita
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAReceitaArrecadada()
{
    parent::TContabilidadeLancamentoReceita();

    $this->setDado('exercicio', Sessao::getExercicio() );
    $this->setDado('exercicio', 2006 );
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
    $stSql .= " SELECT                                                                              \n";
    $stSql .= "     OCR.exercicio,                                                                  \n";
    $stSql .= "     replace(OCR.cod_estrutural,'.','') as estrutural,                               \n";
    $stSql .= "     case when CLR.estorno = true then 2 else 1 end as tipo_lancamento,              \n";
    $stSql .= "     sum( coalesce( CVLD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,   \n";
    $stSql .= "     sum( coalesce( CVLC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito,  \n";
    $stSql .= "     to_char(CLO.dt_lote,'yyyymm') as competencia,                                   \n";
    $stSql .= "     (sum( coalesce( CVLC.vl_lancamento, 0.00 ) ) * ( -1 ) ) - (sum( coalesce( CVLD.vl_lancamento, 0.00 ) ) * ( -1 )) as saldo_arrecadado     \n";
    $stSql .= " FROM                                                                                \n";
    $stSql .= "     orcamento.conta_receita AS OCR                                                  \n";
    $stSql .= "         JOIN orcamento.receita  AS ORE ON(                                          \n";
    $stSql .= "             OCR.cod_conta = ORE.cod_conta   AND                                     \n";
    $stSql .= "             OCR.exercicio = ORE.exercicio                                           \n";
    $stSql .= "         )                                                                           \n";
    $stSql .= "         JOIN contabilidade.lancamento_receita AS CLR ON(                            \n";
    $stSql .= "             ORE.exercicio    = CLR.exercicio    AND                                 \n";
    $stSql .= "             ORE.cod_entidade = CLR.cod_entidade AND                                 \n";
    $stSql .= "             ORE.cod_receita  = CLR.cod_receita                                      \n";
    $stSql .= "         )                                                                           \n";
    $stSql .= "         JOIN (                                                                      \n";
    $stSql .= "             SELECT                                                                  \n";
    $stSql .= "                 CLO.cod_lote,                                                       \n";
    $stSql .= "                 CLO.exercicio,                                                      \n";
    $stSql .= "                 CLO.tipo,                                                           \n";
    $stSql .= "                 CLO.cod_entidade,                                                   \n";
    $stSql .= "                 CLO.dt_lote                                                         \n";
    $stSql .= "             FROM                                                                    \n";
    $stSql .= "                 contabilidade.lote AS CLO                                           \n";
    $stSql .= "             GROUP BY                                                                \n";
    $stSql .= "                 CLO.cod_lote,                                                       \n";
    $stSql .= "                 CLO.exercicio,                                                      \n";
    $stSql .= "                 CLO.tipo,                                                           \n";
    $stSql .= "                 CLO.cod_entidade,                                                   \n";
    $stSql .= "                 CLO.dt_lote                                                         \n";
    $stSql .= "             ORDER BY                                                                \n";
    $stSql .= "                 CLO.cod_lote,                                                       \n";
    $stSql .= "                 CLO.exercicio,                                                      \n";
    $stSql .= "                 CLO.tipo,                                                           \n";
    $stSql .= "                 CLO.cod_entidade,                                                   \n";
    $stSql .= "                 CLO.dt_lote                                                         \n";
    $stSql .= "         ) AS CLO ON(                                                                \n";
    $stSql .= "             CLR.cod_lote     = CLO.cod_lote     AND                                 \n";
    $stSql .= "             CLR.exercicio    = CLO.exercicio    AND                                 \n";
    $stSql .= "             CLR.tipo         = CLO.tipo         AND                                 \n";
    $stSql .= "             CLR.cod_entidade = CLO.cod_entidade                                     \n";
    $stSql .= "         )                                                                           \n";
    $stSql .= "             LEFT JOIN contabilidade.valor_lancamento AS CVLD ON(                    \n";
    $stSql .= "                 CLR.cod_lote       = CVLD.cod_lote      AND                         \n";
    $stSql .= "                 CLR.tipo           = CVLD.tipo          AND                         \n";
    $stSql .= "                 CLR.sequencia      = CVLD.sequencia     AND                         \n";
    $stSql .= "                 CLR.exercicio      = CVLD.exercicio     AND                         \n";
    $stSql .= "                 CLR.cod_entidade   = CVLD.cod_entidade  AND                         \n";
    $stSql .= "                 CLR.estorno       = true                AND                         \n";
    $stSql .= "                 CVLD.tipo         = 'A'                 AND                         \n";
    $stSql .= "                 CVLD.tipo_valor   = 'D'         /*      AND                         \n";
    $stSql .= "                 coalesce( CLO.dt_lote, TO_DATE( '01/01/2006' , 'dd/mm/yyyy' ))      \n";
    $stSql .= "                 BETWEEN TO_DATE('31/12/2006', 'dd/mm/yyyy' ) AND                    \n";
    $stSql .= "                 TO_DATE('01/01/2006', 'dd/mm/yyyy' )    */                          \n";
    $stSql .= "             )                                                                       \n";
    $stSql .= "             LEFT JOIN contabilidade.valor_lancamento AS CVLC ON(                    \n";
    $stSql .= "                 CLR.cod_lote       = CVLC.cod_lote      AND                         \n";
    $stSql .= "                 CLR.tipo           = CVLC.tipo          AND                         \n";
    $stSql .= "                 CLR.sequencia      = CVLC.sequencia     AND                         \n";
    $stSql .= "                 CLR.exercicio      = CVLC.exercicio     AND                         \n";
    $stSql .= "                 CLR.cod_entidade   = CVLC.cod_entidade  AND                         \n";
    $stSql .= "                 CLR.estorno       = false               AND                         \n";
    $stSql .= "                 CVLC.tipo         = 'A'                 AND                         \n";
    $stSql .= "                 CVLC.tipo_valor   = 'C'         /*      AND                         \n";
    $stSql .= "                 coalesce( CLO.dt_lote,  TO_DATE( '01/01/2006', 'dd/mm/yyyy' ) )     \n";
    $stSql .= "                 BETWEEN TO_DATE('01/01/2006', 'dd/mm/yyyy' ) AND                    \n";
    $stSql .= "                 TO_DATE('31/12/2006', 'dd/mm/yyyy' )   */                           \n";
    $stSql .= "             )                                                                       \n";
    $stSql .= " WHERE                                                                               \n";
    $stSql .= "     OCR.exercicio = '".$this->getDado('exercicio')."'                               \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND coalesce( ORE.cod_entidade, 0 ) IN ( 0, ".$this->getDado('stEntidades')." )     \n";
    }
    $stSql .= " GROUP BY                                                                            \n";
    $stSql .= "     OCR.exercicio,                                                                  \n";
    $stSql .= "     OCR.cod_estrutural,                                                             \n";
    $stSql .= "     to_char(CLO.dt_lote,'yyyymm'),                                                  \n";
    $stSql .= "     CLR.estorno                                                                     \n";
    $stSql .= " ORDER BY                                                                            \n";
    $stSql .= "     OCR.exercicio,                                                                  \n";
    $stSql .= "     OCR.cod_estrutural,                                                             \n";
    $stSql .= "     to_char(CLO.dt_lote,'yyyymm')                                                   \n";

    return $stSql;
}

}
