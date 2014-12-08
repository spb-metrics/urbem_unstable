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
    * Data de Criação: 15/08/2007

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
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/21 23:53:13  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 15/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBALicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBALicitacao()
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
    $stSql .= " SELECT   lici.exercicio      \n";
    $stSql .= "         ,lici.cod_licitacao      \n";
    $stSql .= "         ,to_char(pube.data_publicacao,'dd/mm/yyyy') as data_licitacao      \n";
    $stSql .= "         ,cgm.nom_cgm as nom_cgm_imprensa      \n";
    $stSql .= "         ,obj.descricao as descricao_objeto      \n";
    $stSql .= "         ,case   when lici.cod_modalidade = 1 then 5      \n";
    $stSql .= "                 when lici.cod_modalidade = 2 then 10      \n";
    $stSql .= "                 when lici.cod_modalidade = 3 then 1      \n";
    $stSql .= "                 when lici.cod_modalidade = 5 then 4      \n";
    $stSql .= "          end as modalidade      \n";
    $stSql .= "         ,edit.num_edital      \n";
    $stSql .= "         ,to_char(lici.timestamp,'yyyymm') as competencia      \n";
    $stSql .= "         ,lici.vl_cotado      \n";
    $stSql .= "         ,case   when lici.cod_tipo_licitacao = 1 then 3      \n";
    $stSql .= "                 when lici.cod_tipo_licitacao = 2 then 7      \n";
    $stSql .= "                 when lici.cod_tipo_licitacao = 3 then 2      \n";
    $stSql .= "          end as tipo_licitacao      \n";
    $stSql .= "         ,9 as regime_execucao      \n";
    $stSql .= "         ,case when edit.dt_aprovacao_juridico is not null then 1 else 2 end as juridico      \n";
    $stSql .= "         ,to_char(edit.dt_validade_proposta,'dd/mm/yyyy') as data_homologacao      \n";
    $stSql .= "         ,to_char(edit.dt_entrega_propostas,'dd/mm/yyyy') as data_propostas      \n";
    $stSql .= " FROM     licitacao.licitacao            as lici      \n";
    $stSql .= "         ,licitacao.edital               as edit      \n";
    $stSql .= "         ,licitacao.publicacao_edital    as pube      \n";
    $stSql .= "         ,licitacao.veiculos_publicidade as veic      \n";
    $stSql .= "         ,sw_cgm                         as cgm      \n";
    $stSql .= "         ,compras.objeto                 as obj      \n";
    $stSql .= " WHERE   lici.exercicio      = edit.exercicio      \n";
    $stSql .= " AND     lici.cod_entidade   = edit.cod_entidade      \n";
    $stSql .= " AND     lici.cod_modalidade = edit.cod_modalidade      \n";
    $stSql .= " AND     lici.cod_licitacao  = edit.cod_licitacao      \n";
    $stSql .= "       \n";
    $stSql .= " AND     edit.exercicio      = pube.exercicio      \n";
    $stSql .= " AND     edit.num_edital     = pube.num_edital      \n";
    $stSql .= "       \n";
    $stSql .= " AND     pube.numcgm         = veic.numcgm      \n";
    $stSql .= "       \n";
    $stSql .= " AND     veic.numcgm         = cgm.numcgm      \n";
    $stSql .= "       \n";
    $stSql .= " AND     lici.cod_objeto     = obj.cod_objeto      \n";
    $stSql .= "       \n";
    $stSql .= " AND     lici.cod_modalidade NOT IN (8,9)      \n";
    $stSql .= " AND     lici.exercicio  = '".$this->getDado('exercicio')."'    \n";
    if (trim($this->getDado('stEntidades'))) {
        //$stSql .= " AND     lici.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }

    return $stSql;
}

}
