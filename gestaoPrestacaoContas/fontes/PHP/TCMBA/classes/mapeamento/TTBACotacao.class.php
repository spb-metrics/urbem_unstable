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
    * Data de Criação: 18/09/2007

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
Revision 1.3  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/21 01:47:58  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 18/09/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBACotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBACotacao()
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
    $stSql .= " SELECT   coli.exercicio_licitacao as exercicio   \n";
    $stSql .= "         ,coli.cod_licitacao         \n";
    $stSql .= "         ,case when  pf.cpf is not null  then pf.cpf    \n";
    $stSql .= "               when pj.cnpj is not null  then pj.cnpj    \n";
    $stSql .= "                else ''    \n";
    $stSql .= "         end as cpf_cnpj    \n";
    $stSql .= "         ,case when  pf.numcgm is not null then 1    \n";
    $stSql .= "                else 2    \n";
    $stSql .= "         end as pf_pj    \n";
    $stSql .= "         ,cgm.nom_cgm    \n";
    $stSql .= "         ,coli.cod_item    \n";
    $stSql .= "         ,sum(cofi.vl_cotacao) as vl_cotacao    \n";
    $stSql .= "         ,CASE WHEN coji.ordem = 1 then 1 else 2 end as status    \n";
    $stSql .= " FROM     compras.cotacao_fornecedor_item  as cofi    \n";
    $stSql .= "         JOIN  compras.julgamento_item as coji    \n";
    $stSql .= "             ON (    coji.exercicio      = cofi.exercicio    \n";
    $stSql .= "             AND     coji.cod_cotacao    = cofi.cod_cotacao    \n";
    $stSql .= "             AND     coji.cod_item       = cofi.cod_item    \n";
    $stSql .= "             AND     coji.cgm_fornecedor = cofi.cgm_fornecedor    \n";
    $stSql .= "             AND     coji.lote           = cofi.lote    \n";
    $stSql .= "             )    \n";
    $stSql .= "         JOIN  licitacao.cotacao_licitacao   as coli    \n";
    $stSql .= "             ON (    cofi.exercicio      = coli.exercicio_cotacao    \n";
    $stSql .= "             AND     cofi.cod_cotacao    = coli.cod_cotacao    \n";
    $stSql .= "             AND     cofi.cod_item       = coli.cod_item    \n";
    $stSql .= "             AND     cofi.cgm_fornecedor = coli.cgm_fornecedor    \n";
    $stSql .= "             AND     cofi.lote           = coli.lote    \n";
    $stSql .= "             )    \n";
    $stSql .= "         JOIN  sw_cgm  as cgm    \n";
    $stSql .= "             ON ( coli.cgm_fornecedor = cgm.numcgm )    \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_fisica as pf    \n";
    $stSql .= "             ON ( cgm.numcgm = pf.numcgm )    \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_juridica as pj    \n";
    $stSql .= "             ON ( cgm.numcgm = pj.numcgm )    \n";
    $stSql .= " WHERE   coli.exercicio_licitacao = '".$this->getDado('exercicio')."'    \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     coli.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= " GROUP BY coli.exercicio_licitacao \n";
    $stSql .= "         ,coli.cod_licitacao    \n";
    $stSql .= "         ,pf.cpf    \n";
    $stSql .= "         ,pj.cnpj    \n";
    $stSql .= "         ,pf.numcgm    \n";
    $stSql .= "         ,cgm.nom_cgm    \n";
    $stSql .= "         ,coli.cod_item    \n";
    $stSql .= "         ,coji.ordem    \n";

    return $stSql;
}

}
