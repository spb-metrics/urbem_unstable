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
    * Data de Criação: 10/07/2007

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
Revision 1.4  2007/10/13 20:05:49  diego
Corrigindo formatação e informações

Revision 1.3  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.2  2007/07/16 02:41:13  diego
retirado dado fixo de 2006

Revision 1.1  2007/07/11 04:46:53  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

/**
  *
  * Data de Criação: 10/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAEmpenho extends TEmpenhoEmpenho
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAEmpenho()
{
    parent::TEmpenhoEmpenho();

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
    $stSql .= " SELECT   des.exercicio                                                              \n";
    $stSql .= "         ,des.num_orgao                                                              \n";
    $stSql .= "         ,des.num_unidade                                                            \n";
    $stSql .= "         ,des.cod_funcao                                                             \n";
    $stSql .= "         ,des.cod_subfuncao                                                          \n";
    $stSql .= "         ,des.cod_programa                                                           \n";
    $stSql .= "         ,des.num_pao                                                                \n";
    $stSql .= "         ,orcamento.fn_consulta_tipo_pao(des.exercicio,des.num_pao) as tipo_pao      \n";
    $stSql .= "         ,des.cod_recurso                                                            \n";
    $stSql .= "         ,replace(cde.cod_estrutural,'.','') as estrutural                           \n";
    $stSql .= "         ,cgm.nom_cgm                                                                \n";
    $stSql .= "         ,emp.cod_empenho                                                            \n";
    $stSql .= "         ,case when pre.cod_tipo=1 then 3 when pre.cod_tipo=2 then 2 when pre.cod_tipo=3 then 1 end as tipo_empenho \n";
    $stSql .= "         ,to_char(emp.dt_empenho,'dd/mm/yyyy') as dt_empenho                         \n";
    $stSql .= "         ,case when  pf.cpf is not null  then pf.cpf                                 \n";
    $stSql .= "               when pj.cnpj is not null  then pj.cnpj                                \n";
    $stSql .= "                else ''                                                              \n";
    $stSql .= "         end as cpf_cnpj                                                             \n";
    $stSql .= "         ,case when  pf.numcgm is not null then 1                                    \n";
    $stSql .= "                else 2                                                               \n";
    $stSql .= "         end as pf_pj                                                                \n";
    $stSql .= "         ,sume.valor_empenhado                                                       \n";
    $stSql .= " FROM     empenho.empenho             as emp                                         \n";
    $stSql .= "         ,empenho.pre_empenho         as pre                                         \n";
    $stSql .= "         ,sw_cgm                      as cgm                                         \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_fisica as pf                                      \n";
    $stSql .= "             ON ( cgm.numcgm = pf.numcgm )                                           \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_juridica as pj                                    \n";
    $stSql .= "             ON ( cgm.numcgm = pj.numcgm )                                           \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as ped                                         \n";
    $stSql .= "         ,orcamento.conta_despesa     as cde                                         \n";
    $stSql .= "         ,orcamento.despesa           as des                                         \n";
    $stSql .= "         ,(                                                                          \n";
    $stSql .= "             SELECT   exercicio                                                      \n";
    $stSql .= "                     ,cod_pre_empenho                                                \n";
    $stSql .= "                     ,sum(vl_total) as valor_empenhado                               \n";
    $stSql .= "             FROM    empenho.item_pre_empenho as ipe                                 \n";
    $stSql .= "             WHERE   exercicio = '".$this->getDado('exercicio')."'                   \n";
    $stSql .= "             GROUP BY exercicio, cod_pre_empenho                                     \n";
    $stSql .= "         ) as sume                                                                   \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio                                         \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho                                   \n";
    $stSql .= " AND     pre.cgm_beneficiario= cgm.numcgm                                            \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio                                         \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho                                   \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                                         \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa                                       \n";
    $stSql .= " AND     ped.exercicio       = cde.exercicio                                         \n";
    $stSql .= " AND     ped.cod_conta       = cde.cod_conta                                         \n";
    $stSql .= " AND     pre.exercicio       = sume.exercicio                                        \n";
    $stSql .= " AND     pre.cod_pre_empenho = sume.cod_pre_empenho                                  \n";
    $stSql .= " AND     des.exercicio='".$this->getDado('exercicio')."' \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "           AND   emp.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }

    return $stSql;
}

}
