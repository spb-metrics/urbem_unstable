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
    * Data de Criação: 12/08/2007

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

Revision 1.1  2007/08/15 00:21:55  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBARetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBARetencao()
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
    $stSql .= " SELECT   to_char(pag.timestamp,'yyyy') as exercicio    \n";
    $stSql .= "         ,plc.exercicio as exercicio_conta   \n";
    $stSql .= "         ,des.num_orgao    \n";
    $stSql .= "         ,des.num_unidade    \n";
    $stSql .= "         ,emp.cod_empenho    \n";
    $stSql .= "         ,to_char(pag.timestamp,'dd/mm/yyyy') as data_pagamento    \n";
    $stSql .= "         ,plc.cod_estrutural    \n";
    $stSql .= "         ,opr.vl_retencao       \n";
    $stSql .= " FROM     empenho.empenho                as emp    \n";
    $stSql .= "         ,empenho.nota_liquidacao        as liq    \n";
    $stSql .= "     \n";
    $stSql .= "         ,empenho.pagamento_liquidacao   as pli    \n";
    $stSql .= "         ,empenho.ordem_pagamento        as opa    \n";
    $stSql .= "         ,empenho.ordem_pagamento_retencao as opr    \n";
    $stSql .= "     \n";
    $stSql .= "         ,empenho.nota_liquidacao_paga   as pag    \n";
    $stSql .= "         ,contabilidade.plano_analitica  as pla    \n";
    $stSql .= "         ,contabilidade.plano_conta      as plc    \n";
    $stSql .= "         ,empenho.pre_empenho            as pre    \n";
    $stSql .= "         ,empenho.pre_empenho_despesa    as ped    \n";
    $stSql .= "         ,orcamento.despesa              as des    \n";
    $stSql .= " WHERE   emp.exercicio       = pre.exercicio    \n";
    $stSql .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho    \n";
    $stSql .= "     \n";
    $stSql .= " AND     emp.exercicio       = liq.exercicio_empenho    \n";
    $stSql .= " AND     emp.cod_entidade    = liq.cod_entidade    \n";
    $stSql .= " AND     emp.cod_empenho     = liq.cod_empenho    \n";
    $stSql .= "     \n";
    $stSql .= " AND     liq.exercicio       = pli.exercicio    \n";
    $stSql .= " AND     liq.cod_entidade    = pli.cod_entidade    \n";
    $stSql .= " AND     liq.cod_nota        = pli.cod_nota    \n";
    $stSql .= "     \n";
    $stSql .= " AND     pli.exercicio       = opa.exercicio    \n";
    $stSql .= " AND     pli.cod_entidade    = opa.cod_entidade    \n";
    $stSql .= " AND     pli.cod_ordem       = opa.cod_ordem    \n";
    $stSql .= "     \n";
    $stSql .= " AND     opa.exercicio       = opr.exercicio    \n";
    $stSql .= " AND     opa.cod_entidade    = opr.cod_entidade    \n";
    $stSql .= " AND     opa.cod_ordem       = opr.cod_ordem    \n";
    $stSql .= "     \n";
    $stSql .= " AND     opr.exercicio       = pla.exercicio    \n";
    $stSql .= " AND     opr.cod_plano       = pla.cod_plano    \n";
    $stSql .= "     \n";
    $stSql .= " AND     pla.exercicio       = plc.exercicio    \n";
    $stSql .= " AND     pla.cod_conta       = plc.cod_conta    \n";
    $stSql .= "     \n";
    $stSql .= " AND     liq.exercicio       = pag.exercicio    \n";
    $stSql .= " AND     liq.cod_entidade    = pag.cod_entidade    \n";
    $stSql .= " AND     liq.cod_nota        = pag.cod_nota    \n";
    $stSql .= "     \n";
    $stSql .= " AND     pre.exercicio       = ped.exercicio    \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho    \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio    \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa    \n";
    $stSql .= "     \n";
    $stSql .= " AND     to_char(pag.timestamp,'yyyy')  = '".$this->getDado('exercicio')."'    \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     liq.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }

    return $stSql;
}

}
