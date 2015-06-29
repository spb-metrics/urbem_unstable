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
    * Data de Criação: 23/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 23/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPartConv extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPartConv()
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
    $stSql .= " SELECT   conv.exercicio             \n";
    $stSql .= "         ,conv.num_convenio            \n";
    $stSql .= "         ,case when  pf.numcgm is not null then 1            \n";
    $stSql .= "                else 2            \n";
    $stSql .= "         end as pf_pj            \n";
    $stSql .= "         ,case when  pf.cpf is not null  then pf.cpf            \n";
    $stSql .= "               when pj.cnpj is not null  then pj.cnpj            \n";
    $stSql .= "                else ''            \n";
    $stSql .= "         end as cpf_cnpj            \n";
    $stSql .= "         ,cgm.nom_cgm            \n";
    $stSql .= "         ,part.valor_participacao            \n";
    $stSql .= "         ,'' as nome_funcao            \n";
    $stSql .= "         ,to_char(conv.dt_assinatura,'dd/mm/yyyy') as data_assinatura            \n";
    $stSql .= "         ,to_char(conv.dt_vigencia,'dd/mm/yyyy') as data_vigencia            \n";
    $stSql .= " FROM     licitacao.convenio              as conv            \n";
    $stSql .= "         ,licitacao.participante_convenio as part            \n";
    $stSql .= "         ,sw_cgm                          as cgm            \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_fisica as pf            \n";
    $stSql .= "             ON ( cgm.numcgm = pf.numcgm )            \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_juridica as pj            \n";
    $stSql .= "             ON ( cgm.numcgm = pj.numcgm )            \n";
    $stSql .= " WHERE   conv.exercicio      = part.exercicio            \n";
    $stSql .= " AND     conv.num_convenio   = part.num_convenio            \n";
    $stSql .= " AND     part.cgm_fornecedor = cgm.numcgm            \n";
    $stSql .= " AND     conv.exercicio='".$this->getDado('exercicio')."'                    \n";

    return $stSql;
}

}
