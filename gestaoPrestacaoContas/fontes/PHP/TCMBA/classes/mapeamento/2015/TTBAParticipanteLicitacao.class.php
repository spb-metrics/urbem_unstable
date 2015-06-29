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
    * Data de Criação: 31/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/08/02 00:30:06  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipante.class.php" );

/**
  *
  * Data de Criação: 31/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAParticipanteLicitacao extends TLicitacaoParticipante
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAParticipanteLicitacao()
{
    parent::TLicitacaoParticipante();

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
    $stSql .= " SELECT   lici.exercicio         \n";
    $stSql .= "         ,lici.cod_licitacao       \n";
    $stSql .= "         ,case when  pf.numcgm is not null then 1       \n";
    $stSql .= "                else 2       \n";
    $stSql .= "         end as pf_pj_part       \n";
    $stSql .= "         ,case when  pf.cpf is not null  then pf.cpf       \n";
    $stSql .= "               when pj.cnpj is not null  then pj.cnpj       \n";
    $stSql .= "                else ''       \n";
    $stSql .= "         end as cpf_cnpj_part       \n";
    $stSql .= "         ,case when paco.exercicio is not null then 1 else 2 end as tipo_participante       \n";
    $stSql .= "         ,cgm.nom_cgm as nome_participante       \n";
    $stSql .= "         ,pjco.cnpj as cnpj_consorcio    \n";
    $stSql .= "         ,to_char(part.dt_inclusao,'yyyymm') as competencia       \n";
    $stSql .= "         ,CASE WHEN (       \n";
    $stSql .= "             SELECT  count(*) /*qtd_docs_part*/       \n";
    $stSql .= "             FROM    licitacao.participante_documentos as pado       \n";
    $stSql .= "             WHERE   part.exercicio      = pado.exercicio       \n";
    $stSql .= "             AND     part.cod_entidade   = pado.cod_entidade       \n";
    $stSql .= "             AND     part.cod_modalidade = pado.cod_modalidade       \n";
    $stSql .= "             AND     part.cod_licitacao  = pado.cod_licitacao       \n";
    $stSql .= "             AND     part.cgm_fornecedor = pado.cgm_fornecedor       \n";
    $stSql .= "         ) >= (       \n";
    $stSql .= "             SELECT  count(*) /*qtd_docs_lic*/       \n";
    $stSql .= "             FROM    licitacao.licitacao_documentos as lido       \n";
    $stSql .= "             WHERE   lici.exercicio      = lido.exercicio       \n";
    $stSql .= "             AND     lici.cod_entidade   = lido.cod_entidade       \n";
    $stSql .= "             AND     lici.cod_modalidade = lido.cod_modalidade       \n";
    $stSql .= "             AND     lici.cod_licitacao  = lido.cod_licitacao       \n";
    $stSql .= "         ) then 'S' else 'N' end as indicador_habilitacao       \n";
    $stSql .= " FROM     licitacao.licitacao    as lici       \n";
    $stSql .= "         ,sw_cgm                 as cgm       \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_fisica as pf       \n";
    $stSql .= "             ON ( cgm.numcgm = pf.numcgm )       \n";
    $stSql .= "         LEFT JOIN   sw_cgm_pessoa_juridica as pj       \n";
    $stSql .= "             ON ( cgm.numcgm = pj.numcgm )       \n";
    $stSql .= "         ,licitacao.participante as part       \n";
    $stSql .= "         LEFT JOIN       \n";
    $stSql .= "          licitacao.participante_consorcio paco       \n";
    $stSql .= "         ON (    part.exercicio      = paco.exercicio       \n";
    $stSql .= "             AND part.cod_entidade   = paco.cod_entidade       \n";
    $stSql .= "             AND part.cod_modalidade = paco.cod_modalidade       \n";
    $stSql .= "             AND part.cod_licitacao  = paco.cod_licitacao       \n";
    $stSql .= "             AND part.cgm_fornecedor = paco.cgm_fornecedor       \n";
    $stSql .= "         )       \n";
    $stSql .= "        LEFT JOIN      \n";
    $stSql .= "         sw_cgm_pessoa_juridica as pjco  \n";
    $stSql .= "        ON (     \n";
    $stSql .= "            paco.cgm_fornecedor = pjco.numcgm    \n";
    $stSql .= "        )    \n";
    $stSql .= " WHERE   lici.exercicio      = part.exercicio       \n";
    $stSql .= " AND     lici.cod_entidade   = part.cod_entidade       \n";
    $stSql .= " AND     lici.cod_modalidade = part.cod_modalidade       \n";
    $stSql .= " AND     lici.cod_licitacao  = part.cod_licitacao       \n";
    $stSql .= " AND     part.cgm_fornecedor = cgm.numcgm       \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     lici.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= " AND     lici.exercicio='".$this->getDado('exercicio')."'                     \n";

    return $stSql;
}

}
