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
    * Data de Criação: 07/08/2007

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
Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/09 01:05:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 07/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBANotaFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBANotaFiscal()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
    $stSql .= " SELECT   des.exercicio                                      \n";
    $stSql .= "         ,des.num_orgao                                      \n";
    $stSql .= "         ,des.num_unidade                                    \n";
    $stSql .= "         ,emp.cod_empenho                                    \n";
    $stSql .= "         ,to_char(pag.timestamp,'dd/mm/yyyy') as data_pagamento\n";
    $stSql .= "         ,noff.num_nota                                      \n";
    $stSql .= "         ,substr(noff.num_serie,1,3) as serie                \n";
    $stSql .= "         ,substr(noff.num_serie,3,2) as subserie             \n";
    $stSql .= "         ,case when  pf.cpf is not null  then pf.cpf         \n";
    $stSql .= "               when pj.cnpj is not null  then pj.cnpj        \n";
    $stSql .= "                else ''                                      \n";
    $stSql .= "         end as cpf_cnpj                                     \n";
    $stSql .= "         ,cgm.nom_cgm                                        \n";
    $stSql .= "         ,case when  pf.numcgm is not null then 1            \n";
    $stSql .= "                else 2                                       \n";
    $stSql .= "         end as pf_pj                                        \n";
    $stSql .= "         ,to_char(noff.dt_nota,'dd/mm/yyyy') as data_nota    \n";
    $stSql .= "         ,(  SELECT  sum(vl_total)                           \n";
    $stSql .= "             FROM    compras.ordem_compra_item as ocit       \n";
    $stSql .= "             WHERE   ocit.exercicio      = orco.exercicio    \n";
    $stSql .= "             AND     ocit.cod_entidade   = orco.cod_entidade \n";
    $stSql .= "             AND     ocit.cod_ordem      = orco.cod_ordem    \n";
    $stSql .= "         ) as valor_nota                                     \n";
    $stSql .= "         ,noff.observacao                                    \n";
    $stSql .= "         ,to_char(noff.dt_nota,'yyyymm') as competencia      \n";
    $stSql .= " FROM     empenho.empenho             as emp                 \n";
    $stSql .= "         ,empenho.pre_empenho         as pre                 \n";
    $stSql .= "         ,empenho.nota_liquidacao        as liq              \n";
    $stSql .= "         ,empenho.nota_liquidacao_paga   as pag              \n";
    $stSql .= "         ,compras.ordem_compra           as orco             \n";
    $stSql .= "         ,compras.nota_fiscal_fornecedor as noff             \n";
    $stSql .= "         ,sw_cgm                      as cgm                 \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_fisica as pf              \n";
    $stSql .= "             ON ( cgm.numcgm = pf.numcgm )                   \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_juridica as pj            \n";
    $stSql .= "             ON ( cgm.numcgm = pj.numcgm )                   \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as ped                 \n";
    $stSql .= "         ,orcamento.conta_despesa     as cde                 \n";
    $stSql .= "         ,orcamento.despesa           as des                 \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio                 \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho           \n";
    $stSql .= "                                                             \n";
    $stSql .= " AND     emp.exercicio       = liq.exercicio_empenho         \n";
    $stSql .= " AND     emp.cod_entidade    = liq.cod_entidade              \n";
    $stSql .= " AND     emp.cod_empenho     = liq.cod_empenho               \n";
    $stSql .= " AND     liq.exercicio       = pag.exercicio                 \n";
    $stSql .= " AND     liq.cod_entidade    = pag.cod_entidade              \n";
    $stSql .= " AND     liq.cod_nota        = pag.cod_nota                  \n";
    $stSql .= " /**/                                                        \n";
    $stSql .= " AND     emp.exercicio       = orco.exercicio_empenho        \n";
    $stSql .= " AND     emp.cod_entidade    = orco.cod_entidade             \n";
    $stSql .= " AND     emp.cod_empenho     = orco.cod_empenho              \n";
    $stSql .= " AND     orco.exercicio      = noff.exercicio_ordem_compra   \n";
    $stSql .= " AND     orco.cod_entidade   = noff.cod_entidade             \n";
    $stSql .= " AND     orco.cod_ordem      = noff.cod_ordem                \n";
    $stSql .= " /**/                                                        \n";
    $stSql .= " AND     noff.cgm_fornecedor = cgm.numcgm                    \n";
    $stSql .= "                                                             \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio                 \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho           \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                 \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa               \n";
    $stSql .= " AND     ped.exercicio       = cde.exercicio                 \n";
    $stSql .= " AND     ped.cod_conta       = cde.cod_conta                 \n";
    $stSql .= " AND     des.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     des.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= " ORDER BY  des.exercicio                                     \n";
    $stSql .= "         ,des.num_orgao                                      \n";
    $stSql .= "         ,des.num_unidade                                    \n";
    $stSql .= "         ,emp.cod_empenho                                    \n";
    $stSql .= "         ,emp.dt_empenho                                     \n";
    $stSql .= "         ,pag.timestamp                                      \n";

    return $stSql;
}

}
