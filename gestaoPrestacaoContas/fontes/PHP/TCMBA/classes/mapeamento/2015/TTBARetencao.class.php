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

    $Revision: 63436 $
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
    $stSql = " SELECT   to_char(pag.timestamp,'yyyy') as ano    
                         ,plc.exercicio as ano_criacao   
                         ,des.num_orgao AS cod_orgao    
                         ,des.num_unidade AS unidade_orcamentaria  
                         ,emp.cod_empenho AS num_empenho   
                         ,to_char(pag.timestamp,'dd/mm/yyyy') as dt_pagamento_empenho    
                         ,REPLACE(plc.cod_estrutural,'.','') AS conta_contabil
                         ,COALESCE(SUM(opr.vl_retencao),0.00) AS vl_retencao
                         ,1 as tipo_registro
                         ,".$this->getDado('unidade_gestora')." AS unidade_gestora
                         ,".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('inMes')."::VARCHAR AS competencia

                 FROM empenho.empenho                  as emp    
                     ,empenho.nota_liquidacao          as liq    
                     ,empenho.pagamento_liquidacao     as pli    
                     ,empenho.ordem_pagamento          as opa    
                     ,empenho.ordem_pagamento_retencao as opr    
                     ,empenho.nota_liquidacao_paga     as pag    
                     ,contabilidade.plano_analitica    as pla    
                     ,contabilidade.plano_conta        as plc    
                     ,empenho.pre_empenho              as pre    
                     ,empenho.pre_empenho_despesa      as ped    
                     ,orcamento.despesa                as des    

                 WHERE emp.exercicio       = pre.exercicio    
                   AND emp.cod_pre_empenho = pre.cod_pre_empenho    
                     
                   AND emp.exercicio       = liq.exercicio_empenho    
                   AND emp.cod_entidade    = liq.cod_entidade    
                   AND emp.cod_empenho     = liq.cod_empenho    
                     
                   AND liq.exercicio       = pli.exercicio    
                   AND liq.cod_entidade    = pli.cod_entidade    
                   AND liq.cod_nota        = pli.cod_nota    
                     
                   AND pli.exercicio       = opa.exercicio    
                   AND pli.cod_entidade    = opa.cod_entidade    
                   AND pli.cod_ordem       = opa.cod_ordem    
                     
                   AND opa.exercicio       = opr.exercicio    
                   AND opa.cod_entidade    = opr.cod_entidade    
                   AND opa.cod_ordem       = opr.cod_ordem    
                     
                   AND opr.exercicio       = pla.exercicio    
                   AND opr.cod_plano       = pla.cod_plano    
                     
                   AND pla.exercicio       = plc.exercicio    
                   AND pla.cod_conta       = plc.cod_conta    
                     
                   AND liq.exercicio       = pag.exercicio    
                   AND liq.cod_entidade    = pag.cod_entidade    
                   AND liq.cod_nota        = pag.cod_nota    
                     
                   AND pre.exercicio       = ped.exercicio    
                   AND pre.cod_pre_empenho = ped.cod_pre_empenho    
                   AND ped.exercicio       = des.exercicio    
                   AND ped.cod_despesa     = des.cod_despesa    
                     
                   AND to_char(pag.timestamp,'yyyy')  = '".$this->getDado('exercicio')."'
                   AND to_date(to_char(pag.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                   AND liq.cod_entidade IN (".$this->getDado('stEntidades').")

                   GROUP BY pag.timestamp,plc.exercicio,des.num_orgao,des.num_unidade,emp.cod_empenho,plc.cod_estrutural

                   ORDER BY num_empenho,dt_pagamento_empenho
            ";
    return $stSql;
}

}
