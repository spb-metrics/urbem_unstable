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
    * Classe de mapeamento da tabela EMPENHO.PRE_EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-06.02.01

*/

/*
$Log$
Revision 1.5  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

/**
  * Efetua conexão com a tabela  EMPENHO.PRE_EMPENHO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCRJConfiguracao extends TAdministracaoConfiguracao
{
/**
    * Método Construtor
    * @access Private
*/
function TCRJConfiguracao()
{
    parent::TAdministracaoConfiguracao();
    $this->SetDado("exercicio",Sessao::getExercicio());
    $this->SetDado("cod_modulo",32);
}

///**
//    * @access Public
//    * @param  Object  $rsRecordSet Objeto RecordSet
//    * @param  String  $stCondicao  String de condição do SQL (WHERE)
//    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
//    * @param  Boolean $boTransacao
//    * @return Object  Objeto Erro
//*/
//function recuperaRelatorioAutorizacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "") {
//    $obErro      = new Erro;
//    $obConexao   = new Conexao;
//    $rsRecordSet = new RecordSet;
//
//    if(trim($stOrdem))
//        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
//    $stSql = $this->montaRelatorioAutorizacao();
//    $this->setDebug( $stSql );
//    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
//    return $obErro;
//}
//
///**
//    * Seta dados para fazer o recuperaRelacionamento
//    * @access Public
//    * @return String $stSql
//*/
//function montaRelatorioAutorizacao() {
//
//
//    $stSql  = "SELECT                                                                                                    \n";
//    $stSql .= "      tabela.*                                                                                            \n";
//    $stSql .= "     ,publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao                                    \n";
//    $stSql .= "                                WHERE parametro = 'masc_despesa' AND exercicio ='".$this->getDado( "exercicio" )."' ) \n";
//    $stSql .= "     ,tabela.num_orgao                                                                                    \n";
//    $stSql .= "      ||'.'||tabela.num_unidade                                                                           \n";
//    $stSql .= "      ||'.'||tabela.cod_funcao                                                                            \n";
//    $stSql .= "      ||'.'||tabela.cod_subfuncao                                                                         \n";
//    $stSql .= "      ||'.'||tabela.cod_programa                                                                          \n";
//    $stSql .= "      ||'.'||tabela.num_pao                                                                               \n";
//    $stSql .= "      ||'.'||replace(cd.cod_estrutural,'.','')                                                            \n";
//    $stSql .= "      ) AS dotacao                                                                                          \n";
//    $stSql .= "     ,cd.descricao AS nom_conta                                                                          \n";
//    $stSql .= "FROM (                                                                                                    \n";
//    $stSql .= "     SELECT                                                                                               \n";
//    $stSql .= "          tabela.*                                                                                        \n";
//    $stSql .= "         ,CGM.nom_cgm as nom_entidade                                                                     \n";
//    $stSql .= "         ,de.cod_funcao                                                                                   \n";
//    $stSql .= "         ,de.cod_subfuncao                                                                                \n";
//    $stSql .= "         ,de.cod_programa                                                                                 \n";
//    $stSql .= "         ,de.num_pao                                                                                      \n";
//    $stSql .= "         ,de.cod_despesa as dotacao_reduzida                                                              \n";
//    $stSql .= "     FROM                                                                                                 \n";
//    $stSql .= "     (                                                                                                    \n";
//    $stSql .= "         SELECT                                                                                           \n";
//    $stSql .= "              pe.cod_pre_empenho                                                                          \n";
//    $stSql .= "             ,pe.descricao                                                                                \n";
//    $stSql .= "             ,aa.motivo                                                                                   \n";
//    $stSql .= "             ,TO_CHAR(aa.dt_anulacao,'dd/mm/yyyy') as dt_anulacao                                         \n";
//    $stSql .= "             ,ae.cod_entidade                                                                             \n";
//    $stSql .= "             ,to_char(ae.dt_autorizacao, 'dd/mm/yyyy')  as dt_autorizacao                                 \n";
//    $stSql .= "             ,to_char(ae.dt_autorizacao, 'dd/mm/yyyy')  as dt_autorizacao                                 \n";
////  $stSql .= "             ,to_char(it.vl_total,'999999999999999D99') as valor_total                                    \n";
////  $stSql .= "             ,to_char((it.vl_total/it.quantidade),'999999999999999D99') as valor_unitario                 \n";
//    $stSql .= "             ,it.vl_total                 as valor_total                                                  \n";
//    $stSql .= "             ,(it.vl_total/it.quantidade) as valor_unitario                                               \n";
//    $stSql .= "             ,it.num_item                                                                                 \n";
////  $stSql .= "             ,to_char(it.quantidade, '9999999999999D9999') as quantidade                                  \n";
//    $stSql .= "             ,it.quantidade               as quantidade                                                   \n";
//    $stSql .= "             ,it.nom_unidade                                                                              \n";
//    $stSql .= "             ,um.simbolo                                                                                  \n";
//    $stSql .= "             ,it.nom_item                                                                                 \n";
//    $stSql .= "             ,it.complemento                                                                              \n";
//    $stSql .= "             ,oo.num_orgao   ||' - '||oo.nom_orgao   as num_nom_orgao                                     \n";
//    $stSql .= "             ,ou.num_unidade ||' - '||ou.nom_unidade as num_nom_unidade                                   \n";
//    $stSql .= "             ,cg.numcgm as num_fornecedor                                                                 \n";
//    $stSql .= "             ,cg.nom_cgm                                                                                  \n";
//    $stSql .= "             ,oe.numcgm                                                                                   \n";
//    $stSql .= "             ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                                                 \n";
//    $stSql .= "                   ELSE pj.cnpj                                                                           \n";
//    $stSql .= "              END as cpf_cnpj                                                                             \n";
//    $stSql .= "             ,cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco     \n";
//    $stSql .= "             ,mu.nom_municipio                                                                            \n";
//    $stSql .= "             ,CASE WHEN cg.fone_residencial IS NOT NULL THEN cg.fone_residencial                          \n";
//    $stSql .= "                   ELSE cg.fone_comercial                                                                 \n";
//    $stSql .= "              END as telefone                                                                             \n";
//    $stSql .= "             ,uf.sigla_uf                                                                                 \n";
//    $stSql .= "             ,pd.cod_despesa                                                                              \n";
//    $stSql .= "             ,pd.cod_conta                                                                                \n";
//    $stSql .= "             ,ae.exercicio                                                                                \n";
//    $stSql .= "             ,ae.num_orgao                                                                                \n";
//    $stSql .= "             ,TO_CHAR(ore.dt_validade_final ,'dd/mm/yyyy') as dt_validade_final                           \n";
//    $stSql .= "             ,ae.num_unidade                                                                              \n";
////    $stSql .= "             ,cd.descricao AS nom_conta                                                                   \n";
////    $stSql .= "             ,de.cod_despesa as dotacao_reduzida                                                          \n";
//    $stSql .= "         FROM                                                                                             \n";
//    $stSql .= "              empenho.pre_empenho          as pe                                                          \n";
//    $stSql .= "             LEFT JOIN                                                                                    \n";
//    $stSql .= "                    empenho.autorizacao_empenho as ae                                                     \n";
//    $stSql .= "              ON (     ae.cod_pre_empenho  = pe.cod_pre_empenho                                           \n";
//    $stSql .= "                    AND ae.exercicio        = pe.exercicio   )                                            \n";
//    $stSql .= "             LEFT JOIN                                                                                    \n";
//    $stSql .= "                    empenho.autorizacao_reserva as ar                                                     \n";
//    $stSql .= "              ON ( ar.cod_autorizacao = ae.cod_autorizacao AND                                            \n";
//    $stSql .= "                   ar.exercicio       = ae.exercicio       AND                                            \n";
//    $stSql .= "                   ar.cod_entidade    = ae.cod_entidade    )                                              \n";
//    $stSql .= "             LEFT JOIN                                                                                    \n";
//    $stSql .= "                    orcamento.reserva as ore                                                              \n";
//    $stSql .= "              ON ( ore.cod_reserva  =  ar.cod_reserva   AND                                               \n";
//    $stSql .= "                   ore.exercicio    =  ar.exercicio     )                                                 \n";
//    $stSql .= "              LEFT JOIN                                                                                   \n";
//    $stSql .= "                      empenho.autorizacao_anulada as aa                                                   \n";
//    $stSql .= "                   ON (     ae.cod_entidade     = aa.cod_entidade                                         \n";
//    $stSql .= "                        AND ae.exercicio        = aa.exercicio                                            \n";
//    $stSql .= "                        AND ae.cod_autorizacao  = aa.cod_autorizacao )                                    \n";
//    $stSql .= "              LEFT JOIN                                                                                   \n";
//    $stSql .= "                      empenho.pre_empenho_despesa as pd                                                   \n";
//    $stSql .= "                   ON (     pe.cod_pre_empenho   = pd.cod_pre_empenho                                     \n";
//    $stSql .= "                        AND pe.exercicio        = pd.exercicio      )                                     \n";
//    $stSql .= "             ,empenho.item_pre_empenho     as it                                                          \n";
//    $stSql .= "             ,orcamento.unidade            as ou                                                          \n";
//    $stSql .= "             ,orcamento.orgao              as oo                                                          \n";
//    $stSql .= "             ,orcamento.entidade           as oe                                                          \n";
////    $stSql .= "             ,orcamento.despesa            as de                                                          \n";
////    $stSql .= "             ,orcamento.conta_despesa      as cd                                                          \n";
//    $stSql .= "             ,administracao.unidade_medida            as um                                                          \n";
//    $stSql .= "             ,sw_cgm                       as cg                                                          \n";
//    $stSql .= "             LEFT JOIN                                                                                    \n";
//    $stSql .= "              sw_cgm_pessoa_fisica         as pf                                                          \n";
//    $stSql .= "             ON (cg.numcgm = pf.numcgm)                                                                   \n";
//    $stSql .= "             LEFT JOIN                                                                                    \n";
//    $stSql .= "              sw_cgm_pessoa_juridica       as pj                                                          \n";
//    $stSql .= "             ON (cg.numcgm = pj.numcgm)                                                                   \n";
//    $stSql .= "            ,sw_municipio                  as mu                                                          \n";
//    $stSql .= "            ,sw_uf                         as uf                                                          \n";
//    $stSql .= "         WHERE   pe.cod_pre_empenho  = it.cod_pre_empenho                                                 \n";
//    $stSql .= "         AND     pe.exercicio        = it.exercicio                                                       \n";
//    $stSql .= "         AND     pe.cod_pre_empenho  = ae.cod_pre_empenho                                                 \n";
//    $stSql .= "         AND     pe.exercicio        = ae.exercicio                                                       \n";
//    $stSql .= "         --Órgão                                                                                          \n";
//    $stSql .= "         AND     ae.num_orgao        = ou.num_orgao                                                       \n";
//    $stSql .= "         AND     ae.num_unidade      = ou.num_unidade                                                     \n";
//    $stSql .= "         AND     ae.exercicio        = ou.exercicio                                                       \n";
//    $stSql .= "         AND     ou.num_orgao        = oo.num_orgao                                                       \n";
//    $stSql .= "         AND     ou.exercicio        = oo.exercicio                                                       \n";
//    $stSql .= "         --Unidade                                                                                        \n";
//    $stSql .= "         AND     ae.num_orgao        = ou.num_orgao                                                       \n";
//    $stSql .= "         AND     ae.num_unidade      = ou.num_unidade                                                     \n";
//    $stSql .= "         AND     ae.exercicio        = ou.exercicio                                                       \n";
//    $stSql .= "         -- Entidade                                                                                      \n";
//    $stSql .= "         AND     ae.cod_entidade = OE.cod_entidade                                                        \n";
//    $stSql .= "         AND     ae.exercicio    = OE.exercicio                                                           \n";
////    $stSql .= "         --Orcamento/Despesa                                                                              \n";
////    $stSql .= "         AND     pd.cod_despesa      = de.cod_despesa                                                     \n";
////    $stSql .= "         AND     pd.exercicio        = de.exercicio                                                       \n";
////    $stSql .= "         --Conta Despesa                                                                                  \n";
////    $stSql .= "         AND     pd.cod_conta        = cd.cod_conta                                                       \n";
////    $stSql .= "         AND     pd.exercicio        = cd.exercicio                                                       \n";
//    $stSql .= "         --CGM                                                                                            \n";
//    $stSql .= "         AND     pe.cgm_beneficiario = cg.numcgm                                                          \n";
//    $stSql .= "         --Municipio                                                                                      \n";
//    $stSql .= "         AND     cg.cod_municipio    = mu.cod_municipio                                                   \n";
//    $stSql .= "         AND     cg.cod_uf           = mu.cod_uf                                                          \n";
//    $stSql .= "         --Uf                                                                                             \n";
//    $stSql .= "         AND     mu.cod_uf           = uf.cod_uf                                                          \n";
//    $stSql .= "         --Unidade Medida                                                                                 \n";
//    $stSql .= "         AND     it.cod_unidade      = um.cod_unidade                                                     \n";
//    $stSql .= "         AND     it.nom_unidade      = um.nom_unidade                                                     \n";
//    $stSql .= "        " . $this->getDado( "filtro" ) ."                                                                 \n";
//    $stSql .= "         ORDER BY ae.cod_pre_empenho, it.num_item                                                         \n";
//    $stSql .= "     ) as tabela                                                                                          \n";
//    $stSql .= "           LEFT JOIN                                                                                      \n";
//    $stSql .= "                orcamento.despesa as de                                                                   \n";
//    $stSql .= "           ON (    de.cod_despesa = tabela.cod_despesa                                                    \n";
//    $stSql .= "               AND de.exercicio   = tabela.exercicio   )                                                  \n";
//    $stSql .= "          ,sw_cgm  as cgm                                                                                 \n";
//    $stSql .= "     WHERE                                                                                                \n";
//    $stSql .= "          CGM.numcgm          = tabela.numcgm                                                             \n";
//    $stSql .= ") as tabela                                                                                               \n";
//    $stSql .= "     LEFT JOIN                                                                                            \n";
//    $stSql .= "          orcamento.conta_despesa as cd                                                                   \n";
//    $stSql .= "     ON (    cd.cod_conta  = tabela.cod_conta                                                             \n";
//    $stSql .= "         AND cd.exercicio  = tabela.exercicio   )                                                         \n";
////    $stSql .= "WHERE                                                                                                     \n";
////   mostravar( $stSql );
////   die();
//    return $stSql;
//
//}
//
///**
//    * Seta os dados pra fazer o recuperaSaldoAnterior
//    * @access Private
//    * @return $stSql
//*/
//function montaRecuperaSaldoAnterior() {
//    $stSql  = "SELECT                                                              \n";
//    $stSql .= "  empenho.fn_saldo_dotacao (                                    \n";
//    $stSql .= "                               '".$this->getDado( "exercicio" )."'  \n";
//    $stSql .= "                               ,".$this->getDado( "cod_despesa" )." \n";
//    $stSql .= "                               ) AS saldo_anterior                  \n";
//    return $stSql;
//}
//
//
///**
//    * @access Public
//    * @param  Object  $rsRecordSet Objeto RecordSet
//    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
//    * @param  Boolean $boTransacao
//    * @return Object  Objeto Erro
//*/
//function recuperaSaldoAnterior(&$rsRecordSet, $stOrdem = "" , $boTransacao = "") {
//    $obErro      = new Erro;
//    $obConexao   = new Conexao;
//    $rsRecordSet = new RecordSet;
//
//    if(trim($stOrdem))
//        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
//    $stSql = $this->montaRecuperaSaldoAnterior();
//    $this->setDebug( $stSql );
//    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
//    return $obErro;
//}
//
//    /**
//        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
//        * @access Public
//        * @param  Object  $rsRecordSet Objeto RecordSet
//        * @param  String  $stCondicao  String de condição do SQL (WHERE)
//        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
//        * @param  Boolean $boTransacao
//        * @return Object  Objeto Erro
//    */
//    function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "") {
//        $obErro      = new Erro;
//        $obConexao   = new Conexao;
//        $rsRecordSet = new RecordSet;
//        if(trim($stOrdem))
//            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
//        $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
//        $this->setDebug( $stSql );
//        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
//        return $obErro;
//    }
//
//    function MontaRecuperaDadosExportacao() {
//        $stSql   = "";
//        $stSql .= "SELECT                                                                                                          \n";
//        $stSql .= "    SW.numcgm,                                                                                                  \n";
//        $stSql .= "    SW.nom_cgm,                                                                                                 \n";
//        $stSql .= "    CASE WHEN PF.numcgm IS NOT NULL THEN replace(replace(replace(PF.cpf,'-',''),'\r',''),'\n','') ELSE replace(replace(replace(PJ.cnpj,'-',''),'\r',''),'\n','') END AS cpf_cnpj,   \n";
//        $stSql .= "    CASE                                                                                                        \n";
//        $stSql .= "        WHEN PJ.insc_estadual = '' THEN '0'                                                                     \n";
//        $stSql .= "        ELSE replace(PJ.insc_estadual,'-','')                                                                   \n";
//        $stSql .= "    END as insc_estadual,                                                                                       \n";
//        $stSql .= "    CASE                                                                                                        \n";
//        $stSql .= "        WHEN EF.inscricao_economica IS NOT NULL THEN replace(EF.inscricao_economica,'-','')                     \n";
//        $stSql .= "        WHEN ED.inscricao_economica IS NOT NULL THEN replace(ED.inscricao_economica,'-','')                     \n";
//        $stSql .= "        WHEN EA.inscricao_economica IS NOT NULL THEN replace(EA.inscricao_economica,'-','')                     \n";
//        $stSql .= "        ELSE ''                                                                                                 \n";
//        $stSql .= "    END AS insc_municipal,                                                                                      \n";
//        $stSql .= "    SW.tipo_logradouro||' '||SW.logradouro||' n:'||SW.numero||' '||SW.complemento||' '||SW.bairro  AS endereco, \n";
//        $stSql .= "    SM.nom_municipio,                                                                                           \n";
//        $stSql .= "    SF.sigla_uf as nom_uf,                                                                                      \n";
//        $stSql .= "    SW.cep,                                                                                                     \n";
//        $stSql .= "    SW.fone_comercial,                                                                                          \n";
//        $stSql .= "    '' AS fax,                                                                                                  \n";
//        $stSql .= "    TC.tipo                                                                                                     \n";
//        $stSql .= "FROM                                                                                                            \n";
//        $stSql .= "    sw_municipio            AS SM,                                                                              \n";
//        $stSql .= "    sw_uf                   AS SF,                                                                              \n";
//        $stSql .= "    tcers.credor            AS TC,                                                                              \n";
//        $stSql .= "    sw_cgm                  AS SW                                                                               \n";
//        $stSql .= "LEFT JOIN                                                                                                       \n";
//        $stSql .= "    sw_cgm_pessoa_fisica    AS PF                                                                               \n";
//        $stSql .= "ON                                                                                                              \n";
//        $stSql .= "    SW.numcgm     = PF.numcgm                                                                                   \n";
//        $stSql .= "LEFT JOIN                                                                                                       \n";
//        $stSql .= "    sw_cgm_pessoa_juridica    AS PJ                                                                             \n";
//        $stSql .= "ON                                                                                                              \n";
//        $stSql .= "    SW.numcgm     = PJ.numcgm                                                                                   \n";
//        $stSql .= "LEFT JOIN                                                                                                       \n";
//        $stSql .= "    economico.cadastro_economico_empresa_fato AS EF                                                             \n";
//        $stSql .= "ON                                                                                                              \n";
//        $stSql .= "    EF.numcgm               = SW.numcgm                                                                         \n";
//        $stSql .= "LEFT JOIN                                                                                                       \n";
//        $stSql .= "    economico.cadastro_economico_empresa_direito AS ED                                                          \n";
//        $stSql .= "ON                                                                                                              \n";
//        $stSql .= "    ED.numcgm               = SW.numcgm                                                                         \n";
//        $stSql .= "LEFT JOIN                                                                                                       \n";
//        $stSql .= "    economico.cadastro_economico_autonomo AS EA                                                                 \n";
//        $stSql .= "ON                                                                                                              \n";
//        $stSql .= "    EA.numcgm               = SW.numcgm                                                                         \n";
//        $stSql .= "WHERE                                                                                                           \n";
//        $stSql .= "    SW.numcgm               = TC.numcgm AND                                                                     \n";
//        $stSql .= "    SW.cod_municipio        = SM.cod_municipio AND                                                              \n";
//        $stSql .= "    SW.cod_uf               = SM.cod_uf AND                                                                     \n";
//        $stSql .= "    SM.cod_uf               = SF.cod_uf AND                                                                     \n";
//        $stSql .= "    TC.exercicio            = ".$this->getDado("inExercicio")." AND                                             \n";
//        $stSql .= "    TC.numcgm in                                                                                                \n";
//        $stSql .= "        (SELECT                                                                                                 \n";
//        $stSql .= "            EP.cgm_beneficiario                                                                                 \n";
//        $stSql .= "        FROM                                                                                                    \n";
//        $stSql .= "            empenho.empenho         AS EE,                                                                      \n";
//        $stSql .= "            empenho.pre_empenho     AS EP                                                                       \n";
//        $stSql .= "        WHERE                                                                                                   \n";
//        $stSql .= "            EE.exercicio            = EP.exercicio AND                                                          \n";
//        $stSql .= "            EE.cod_pre_empenho      = EP.cod_pre_empenho)                                                       \n";
//        return $stSql;
//    }
//
}
