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
  * Classe de mapeamento da tabela pessoal.assentamento_acao
  * Data de Criação: 24/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name:  $
    $Author: souzadl $
    $Date: 2008-03-11 14:34:27 -0300 (Ter, 11 Mar 2008) $

    Caso de uso: uc-04.04.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAssentamentoGerado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_gerado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento_gerado, timestamp');

    $this->AddCampo('cod_assentamento_gerado' , 'integer'       ,  true,    '',  true,  true);
    $this->AddCampo('timestamp'               , 'timestamp_now' ,  true,    '',  true, false);
    $this->AddCampo('cod_assentamento'        , 'integer'       ,  true,    '', false,  true);
    $this->AddCampo('observacao'              , 'char'          , false, '200', false, false);
    $this->AddCampo('automatico'              , 'boolean'       ,  true,    '', false, false);
    $this->AddCampo('periodo_inicial'         , 'date'          ,  true,    '', false, false);
    $this->AddCampo('periodo_final'           , 'date'          , false,    '', false, false);
}

function montaRecuperaRelacionamento()
{
    $stSql  ="    SELECT assentamento_gerado_contrato_servidor.cod_contrato                                                                                                                  \n";
    $stSql .="     , assentamento_gerado.cod_assentamento_gerado                                                                                                                             \n";
    $stSql .="     , assentamento_gerado.timestamp                                                                                                                                           \n";
    $stSql .="     , assentamento_gerado.cod_assentamento                                                                                                                                    \n";
    $stSql .="     , to_char(assentamento_gerado.periodo_inicial,'dd/mm/yyyy') as periodo_inicial                                                                                            \n";
    $stSql .="     , to_char(assentamento_gerado.periodo_final  ,'dd/mm/yyyy') as periodo_final                                                                                              \n";
    $stSql .="     , to_char(assentamento_licenca_premio.dt_inicial,'dd/mm/yyyy') as dt_inicial                                                                                              \n";
    $stSql .="     , to_char(assentamento_licenca_premio.dt_final  ,'dd/mm/yyyy') as dt_final                                                                                                \n";
    $stSql .="     , assentamento_gerado.automatico                                                                                                                                          \n";
    $stSql .="     , assentamento_gerado.observacao                                                                                                                                          \n";
    $stSql .="     , trim(assentamento_assentamento.descricao) as descricao_assentamento                                                                                                     \n";
    $stSql .="     , assentamento_assentamento.cod_classificacao                                                                                                                             \n";
    $stSql .="     , classificacao_assentamento.descricao as descricao_classificacao                                                                                                         \n";
    $stSql .="     , contrato.registro                                                                                                                                                       \n";
    $stSql .="     , contrato.numcgm                                                                                                                                                         \n";
    $stSql .="     , contrato.nom_cgm                                                                                                                                                        \n";
    $stSql .="     , contrato.descricao_lotacao                                                                                                                                              \n";
    $stSql .="     , contrato.cod_estrutural                                                                                                                                                 \n";
    $stSql .="     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao                                                                     \n";
    $stSql .="  FROM pessoal.assentamento_gerado                                                                                                                                             \n";
    $stSql .="                                                                                                                                                                               \n";
    $stSql .="LEFT JOIN pessoal.assentamento_licenca_premio                                                                                                                                  \n";
    $stSql .="       ON assentamento_licenca_premio.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                                                                    \n";
    $stSql .="      AND assentamento_licenca_premio.timestamp = assentamento_gerado.timestamp                                                                                                \n";
    $stSql .="                                                                                                                                                                               \n";
    $stSql .="     , (SELECT cod_assentamento_gerado                                                                                                                                         \n";
    $stSql .="             , max(timestamp) as timestamp                                                                                                                                     \n";
    $stSql .="          FROM pessoal.assentamento_gerado                                                                                                                                     \n";
    $stSql .="       GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                                                                                                            \n";
    $stSql .="     , pessoal.assentamento_gerado_contrato_servidor                                                                                                                           \n";
    $stSql .="  LEFT JOIN                                                                                                                                                                    \n";
    $stSql .="	(  (SELECT contrato.registro                                                                                                                                                 \n";
    $stSql .="                , contrato.cod_contrato                                                                                                                                        \n";
    $stSql .="                , sw_cgm.numcgm                                                                                                                                                \n";
    $stSql .="                , sw_cgm.nom_cgm                                                                                                                                               \n";
    $stSql .="                , recuperaDescricaoOrgao(vw_orgao_nivel.cod_orgao,'".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                     \n";
    $stSql .="                , vw_orgao_nivel.orgao as cod_estrutural                                                                                                                       \n";
    $stSql .="                , contrato_servidor.ativo                                                                                                                                      \n";
    $stSql .="                , contrato_servidor.cod_cargo                                                                                                                                  \n";
    $stSql .="                , contrato_servidor_especialidade_cargo.cod_especialidade                                                                                                      \n";
    $stSql .="                , contrato_servidor_funcao.cod_funcao                                                                                                                          \n";
    $stSql .="                , contrato_servidor_especialidade_funcao.cod_especialidade_funcao                                                                                              \n";
    $stSql .="             FROM pessoal.contrato                                                                                                                                             \n";
    $stSql .="                , pessoal.servidor_contrato_servidor                                                                                                                           \n";
    $stSql .="                , pessoal.servidor                                                                                                                                             \n";
    $stSql .="                , sw_cgm                                                                                                                                                       \n";
    $stSql .="                , pessoal.contrato_servidor_orgao                                                                                                                              \n";
    $stSql .="                , (  SELECT cod_contrato                                                                                                                                       \n";
    $stSql .="                          , max(timestamp) as timestamp                                                                                                                        \n";
    $stSql .="                       FROM pessoal.contrato_servidor_orgao                                                                                                                    \n";
    $stSql .="                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                       \n";
    $stSql .="                , organograma.vw_orgao_nivel                                                                                                                                   \n";
    $stSql .="                , pessoal.contrato_servidor                                                                                                                                    \n";
    $stSql .="        LEFT JOIN pessoal.contrato_servidor_especialidade_cargo                                                                                                                \n";
    $stSql .="               ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                                                          \n";
    $stSql .="        LEFT JOIN (SELECT contrato_servidor_funcao.cod_contrato                                                                                                                \n";
    $stSql .="                        , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                                     \n";
    $stSql .="                     FROM pessoal.contrato_servidor_funcao                                                                                                                     \n";
    $stSql .="                        , (  SELECT cod_contrato                                                                                                                               \n";
    $stSql .="                                  , max(timestamp) as timestamp                                                                                                                \n";
    $stSql .="                               FROM pessoal.contrato_servidor_funcao                                                                                                           \n";
    $stSql .="                           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                              \n";
    $stSql .="                    WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                    \n";
    $stSql .="                      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp) as contrato_servidor_funcao                                             \n";
    $stSql .="               ON contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                       \n";
    $stSql .="        LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                                  \n";
    $stSql .="                        , contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao                                                                 \n";
    $stSql .="                     FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                       \n";
    $stSql .="                        , (  SELECT cod_contrato                                                                                                                               \n";
    $stSql .="                                  , max(timestamp) as timestamp                                                                                                                \n";
    $stSql .="                               FROM pessoal.contrato_servidor_especialidade_funcao                                                                                             \n";
    $stSql .="                           GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                \n";
    $stSql .="                    WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                        \n";
    $stSql .="                      AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao   \n";
    $stSql .="               ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                         \n";
    $stSql .="            WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                                              \n";
    $stSql .="              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                              \n";
    $stSql .="              AND servidor.numcgm = sw_cgm.numcgm                                                                                                                              \n";
    $stSql .="              AND contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                                 \n";
    $stSql .="              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                              \n";
    $stSql .="              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                                                    \n";
    $stSql .="              AND contrato_servidor_orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                                                 \n";
    $stSql .="              AND contrato.cod_contrato = contrato_servidor.cod_contrato                                                                                                       \n";
    $stSql .="        )                                                                                                                                                                      \n";
    $stSql .="		UNION                                                                                                                                                                    \n";
    $stSql .="		( SELECT  contrato.registro                                                                                                                                              \n";
    $stSql .="                , contrato.cod_contrato                                                                                                                                        \n";
    $stSql .="                , sw_cgm.numcgm                                                                                                                                                \n";
    $stSql .="                , sw_cgm.nom_cgm                                                                                                                                               \n";
    $stSql .="                , recuperaDescricaoOrgao(vw_orgao_nivel.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                                          \n";
    $stSql .="                , vw_orgao_nivel.orgao as cod_estrutural                                                                                                                       \n";
    $stSql .="                , contrato_servidor.ativo                                                                                                                                      \n";
    $stSql .="                , contrato_servidor.cod_cargo                                                                                                                                  \n";
    $stSql .="                , contrato_servidor_especialidade_cargo.cod_especialidade                                                                                                      \n";
    $stSql .="                , contrato_servidor_funcao.cod_funcao                                                                                                                          \n";
    $stSql .="                , contrato_servidor_especialidade_funcao.cod_especialidade_funcao                                                                                              \n";
    $stSql .="             FROM pessoal.contrato                                                                                                                                             \n";
    $stSql .="                , pessoal.pensionista                                                                                                                                          \n";
    $stSql .="                , pessoal.servidor_contrato_servidor                                                                                                                           \n";
    $stSql .="                , pessoal.servidor                                                                                                                                             \n";
    $stSql .="                , sw_cgm                                                                                                                                                       \n";
    $stSql .="                , pessoal.contrato_pensionista_orgao                                                                                                                           \n";
    $stSql .="                , (  SELECT cod_contrato                                                                                                                                       \n";
    $stSql .="                          , max(timestamp) as timestamp                                                                                                                        \n";
    $stSql .="                       FROM pessoal.contrato_pensionista_orgao                                                                                                                 \n";
    $stSql .="                   GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                    \n";
    $stSql .="                , organograma.vw_orgao_nivel                                                                                                                                   \n";
    $stSql .="			    , pessoal.contrato_servidor                                                                                                                                      \n";
    $stSql .="                , pessoal.contrato_pensionista                                                                                                                                 \n";
    $stSql .="        LEFT JOIN pessoal.contrato_servidor_especialidade_cargo                                                                                                                \n";
    $stSql .="               ON contrato_servidor_especialidade_cargo.cod_contrato = contrato_pensionista.cod_contrato_cedente                                                               \n";
    $stSql .="        LEFT JOIN (SELECT contrato_servidor_funcao.cod_contrato                                                                                                                \n";
    $stSql .="                        , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                                     \n";
    $stSql .="                     FROM pessoal.contrato_servidor_funcao                                                                                                                     \n";
    $stSql .="                        , (  SELECT cod_contrato                                                                                                                               \n";
    $stSql .="                                  , max(timestamp) as timestamp                                                                                                                \n";
    $stSql .="                               FROM pessoal.contrato_servidor_funcao                                                                                                           \n";
    $stSql .="                           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                              \n";
    $stSql .="                    WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                    \n";
    $stSql .="                      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp) as contrato_servidor_funcao                                             \n";
    $stSql .="               ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_funcao.cod_contrato                                                                            \n";
    $stSql .="        LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                                  \n";
    $stSql .="                        , contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao                                                                 \n";
    $stSql .="                     FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                       \n";
    $stSql .="                        , (  SELECT cod_contrato                                                                                                                               \n";
    $stSql .="                                  , max(timestamp) as timestamp                                                                                                                \n";
    $stSql .="                               FROM pessoal.contrato_servidor_especialidade_funcao                                                                                             \n";
    $stSql .="                           GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                \n";
    $stSql .="                    WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                        \n";
    $stSql .="                      AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao   \n";
    $stSql .="               ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_especialidade_funcao.cod_contrato                                                              \n";
    $stSql .="            WHERE contrato.cod_contrato = contrato_pensionista.cod_contrato                                                                                                    \n";
    $stSql .="			  AND pensionista.cod_pensionista = contrato_pensionista.cod_pensionista                                                                                             \n";
    $stSql .="			  AND contrato_pensionista.cod_contrato_cedente = servidor_contrato_servidor.cod_contrato                                                                            \n";
    $stSql .="              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                              \n";
    $stSql .="              AND pensionista.numcgm = sw_cgm.numcgm                                                                                                                           \n";
    $stSql .="              AND contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                                              \n";
    $stSql .="              AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                        \n";
    $stSql .="              AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                                                                              \n";
    $stSql .="              AND contrato_pensionista_orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                                              \n";
    $stSql .="              AND contrato_pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato                                                                                   \n";
    $stSql .="        )                                                                                                                                                                      \n";
    $stSql .="	) as contrato                                                                                                                                                                \n";
    $stSql .="       ON assentamento_gerado_contrato_servidor.cod_contrato = contrato.cod_contrato                                                                                           \n";
    $stSql .="     , pessoal.assentamento_assentamento                                                                                                                                       \n";
    $stSql .="LEFT JOIN pessoal.classificacao_assentamento                                                                                                                                   \n";
    $stSql .="       ON assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                                                                           \n";
    $stSql .="LEFT JOIN pessoal.tipo_classificacao                                                                                                                                           \n";
    $stSql .="       ON classificacao_assentamento.cod_tipo = tipo_classificacao.cod_tipo                                                                                                    \n";
    $stSql .=" WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado                                                             \n";
    $stSql .="   AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento                                                                                       \n";
    $stSql .="   AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado                                                                                      \n";
    $stSql .="                                                             FROM pessoal.assentamento_gerado_excluido)                                                                        \n";
    $stSql .="   AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                                                           \n";
    $stSql .="   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                                                       \n";

    return $stSql;
}

function recuperaAssentamentoSEFIP(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY assentamento_gerado_contrato_servidor.cod_contrato";
    $stSql = $this->montaRecuperaAssentamentoSEFIP().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoSEFIP()
{
    $stSql .= "SELECT assentamento_gerado_contrato_servidor.*                                                                                                  \n";
    $stSql .= "     , periodo_inicial                                                                                                                          \n";
    $stSql .= "     , periodo_final                                                                                                                            \n";
    $stSql .= "     , assentamento_gerado.timestamp                                                                                                            \n";
    $stSql .= "     , assentamento_gerado.cod_assentamento                                                                                                            \n";
    $stSql .= "     , classificacao_assentamento.cod_tipo \n";
    $stSql .= "  FROM pessoal.assentamento_gerado_contrato_servidor                                                                                            \n";
    $stSql .= "     , pessoal.assentamento_gerado                                                                                                              \n";
    $stSql .= "     , (SELECT cod_assentamento_gerado                                                                                                          \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                      \n";
    $stSql .= "          FROM pessoal.assentamento_gerado                                                                                                      \n";
    $stSql .= "       GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                                                                             \n";
    $stSql .= "     , pessoal.assentamento                                                                                                                     \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                                                                 \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                      \n";
    $stSql .= "          FROM pessoal.assentamento                                                                                                             \n";
    $stSql .= "       GROUP BY cod_assentamento) as max_assentamento                                                                                           \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                                                                                        \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                                                                                       \n";
    $stSql .= " WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                              \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                            \n";
    $stSql .= "   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                        \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                     \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                        \n";
    $stSql .= "   AND assentamento.timestamp = max_assentamento.timestamp                                                                                      \n";
    $stSql .= "   AND assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                                                               \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                                               \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                     \n";
    $stSql .= "                     FROM pessoal.assentamento_gerado_excluido                                                                                  \n";
    $stSql .= "                    WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                    \n";
    $stSql .= "                      AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                               \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                     \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                                                                                  \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato \n";
    $stSql .= "                      AND to_char(dt_rescisao,'yyyy-mm') != '".$this->getDado("competencia1")."'  \n";
    $stSql .= "                      AND to_char(dt_rescisao,'yyyy-mm') != '".$this->getDado("competencia2")."')                       \n";

    return $stSql;
}

function recuperaAssentamentoSEFIPTemporario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY assentamento_gerado_contrato_servidor.cod_contrato";
    $stSql = $this->montaRecuperaAssentamentoSEFIPTemporario().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoSEFIPTemporario()
{
    $stSql .= "SELECT assentamento_gerado_contrato_servidor.*                                                                                                  \n";
    $stSql .= "     , (SELECT trim(num_sefip) FROM pessoal.sefip WHERE cod_sefip = assentamento_mov_sefip_saida.cod_sefip_saida) as num_sefip                        \n";
    $stSql .= "     , (SELECT repetir_mensal FROM pessoal.sefip WHERE cod_sefip = assentamento_mov_sefip_saida.cod_sefip_saida) as repetir_mensal              \n";
    $stSql .= "     , periodo_inicial                                                                                                                          \n";
    $stSql .= "     , periodo_final                                                                                                                            \n";
    $stSql .= "     , assentamento_mov_sefip_saida.cod_sefip_saida                                                                          \n";
    $stSql .= "     , assentamento_gerado.timestamp                                                                                                            \n";
    $stSql .= "     , assentamento_gerado.cod_assentamento                                                                                                            \n";
    $stSql .= "     , classificacao_assentamento.cod_tipo \n";
    $stSql .= "  FROM pessoal.assentamento_gerado_contrato_servidor                                                                                            \n";
    $stSql .= "     , pessoal.assentamento_gerado                                                                                                              \n";
    $stSql .= "     , (SELECT cod_assentamento_gerado                                                                                                          \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                      \n";
    $stSql .= "          FROM pessoal.assentamento_gerado                                                                                                      \n";
    $stSql .= "       GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                                                                             \n";
    $stSql .= "     , pessoal.assentamento                                                                                                                     \n";
    $stSql .= "     , pessoal.assentamento_mov_sefip_saida                                                                                                     \n";
    $stSql .= "     , (SELECT cod_assentamento                                                                                                                 \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                      \n";
    $stSql .= "          FROM pessoal.assentamento                                                                                                             \n";
    $stSql .= "       GROUP BY cod_assentamento) as max_assentamento                                                                                           \n";
    $stSql .= "     , pessoal.assentamento_assentamento                                                                                                        \n";
    $stSql .= "     , pessoal.classificacao_assentamento                                                                                                       \n";
    $stSql .= " WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                              \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                            \n";
    $stSql .= "   AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                        \n";
    $stSql .= "   AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                     \n";
    $stSql .= "   AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                        \n";
    $stSql .= "   AND assentamento.timestamp = max_assentamento.timestamp                                                                                      \n";
    $stSql .= "   AND assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                                                               \n";
    $stSql .= "   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                                               \n";
    $stSql .= "   AND assentamento.cod_assentamento = assentamento_mov_sefip_saida.cod_assentamento                                                            \n";
    $stSql .= "   AND assentamento.timestamp = assentamento_mov_sefip_saida.timestamp                                                                          \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                     \n";
    $stSql .= "                     FROM pessoal.assentamento_gerado_excluido                                                                                  \n";
    $stSql .= "                    WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                    \n";
    $stSql .= "                      AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                               \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                     \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                                                                                  \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato \n";
    $stSql .= "                      AND to_char(dt_rescisao,'yyyy-mm') != '".$this->getDado("competencia1")."'  \n";
    $stSql .= "                      AND to_char(dt_rescisao,'yyyy-mm') != '".$this->getDado("competencia2")."')                       \n";

    return $stSql;
}

function recuperaEventosAssentamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaEventosAssentamento",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaEventosAssentamento()
{
    $stSql .= "SELECT assentamento_gerado.cod_assentamento                                                                                                 \n";
    $stSql .= "     , assentamento_evento.cod_evento                                                                                          \n";
    $stSql .= "     , evento.tipo                                                                                                                          \n";
    $stSql .= "     , periodo_inicial                                                                                                                      \n";
    $stSql .= "     , periodo_final                                                                                                                        \n";
    $stSql .= " FROM pessoal.assentamento_gerado_contrato_servidor                                                               \n";
    $stSql .= "    , pessoal.assentamento_gerado                                                                                 \n";
    $stSql .= "    , (   SELECT cod_assentamento_gerado                                                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento_gerado                                                                      \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                        \n";
    $stSql .= "    , pessoal.assentamento                                                                                        \n";
    $stSql .= "    , (   SELECT cod_assentamento                                                                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento                                                                             \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento                                                                                      \n";
    $stSql .= "    , pessoal.assentamento_assentamento                                                                                        \n";
    $stSql .= "    , pessoal.assentamento_evento                                                                    \n";
    $stSql .= "    , folhapagamento.evento                                                                                       \n";
    $stSql .= "WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                           \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                         \n";
    $stSql .= "  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                     \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                  \n";
    $stSql .= "  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                     \n";
    $stSql .= "  AND assentamento.timestamp = max_assentamento.timestamp                                                                                   \n";
    $stSql .= "  AND assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                                                     \n";
    $stSql .= "  AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                                     \n";
    $stSql .= "  AND assentamento.timestamp = assentamento_evento.timestamp                                                                   \n";
    $stSql .= "  AND assentamento_evento.cod_evento= evento.cod_evento                                                                        \n";
    $stSql .= "  AND NOT EXISTS (SELECT *                                                                                                                  \n";
    $stSql .= "                    FROM pessoal.assentamento_gerado_excluido                                                     \n";
    $stSql .= "                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                 \n";
    $stSql .= "                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                            \n";

    return $stSql;
}

function recuperaEventosPorporcionaisAssentamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaEventosPorporcionaisAssentamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosPorporcionaisAssentamento()
{
    $stSql .= "SELECT assentamento_gerado.cod_assentamento                                                                                                 \n";
    $stSql .= "     , assentamento_evento_proporcional.cod_evento                                                                                          \n";
    $stSql .= "     , evento.tipo                                                                                                                          \n";
    $stSql .= "     , periodo_inicial                                                                                                                      \n";
    $stSql .= "     , periodo_final                                                                                                                        \n";
    $stSql .= " FROM pessoal.assentamento_gerado_contrato_servidor                                                                                         \n";
    $stSql .= "    , pessoal.assentamento_gerado                                                                                                           \n";
    $stSql .= "    , (   SELECT cod_assentamento_gerado                                                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento_gerado                                                                                                \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                        \n";
    $stSql .= "    , pessoal.assentamento                                                                                                                  \n";
    $stSql .= "    , (   SELECT cod_assentamento                                                                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento                                                                                                       \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento                                                                                      \n";
    $stSql .= "    , pessoal.assentamento_evento_proporcional                                                                                              \n";
    $stSql .= "    , folhapagamento.evento                                                                                                                 \n";
    $stSql .= "WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                           \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                         \n";
    $stSql .= "  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                     \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                  \n";
    $stSql .= "  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                     \n";
    $stSql .= "  AND assentamento.timestamp = max_assentamento.timestamp                                                                                   \n";
    $stSql .= "  AND assentamento.cod_assentamento = assentamento_evento_proporcional.cod_assentamento                                                     \n";
    $stSql .= "  AND assentamento.timestamp = assentamento_evento_proporcional.timestamp                                                                   \n";
    $stSql .= "  AND assentamento_evento_proporcional.cod_evento= evento.cod_evento                                                                        \n";
    $stSql .= "  AND NOT EXISTS (SELECT *                                                                                                                  \n";
    $stSql .= "                    FROM pessoal.assentamento_gerado_excluido                                                                               \n";
    $stSql .= "                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                 \n";
    $stSql .= "                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                            \n";

    return $stSql;
}

function recuperaAssentamentoGerado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaAssentamentoGerado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoGerado()
{
    $stSql .= "SELECT assentamento_gerado.*                                                                                                                \n";
    $stSql .= " FROM pessoal.assentamento_gerado_contrato_servidor                                                                \n";
    $stSql .= "    , pessoal.assentamento_gerado                                                                                  \n";
    $stSql .= "INNER JOIN pessoal.assentamento_assentamento                                                                       \n";
    $stSql .= "       ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento                                                 \n";
    $stSql .= "INNER JOIN pessoal.classificacao_assentamento                                                                      \n";
    $stSql .= "        ON assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                                       \n";
    $stSql .= "    , (   SELECT cod_assentamento_gerado                                                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento_gerado                                                                       \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                        \n";
    $stSql .= "    , pessoal.assentamento                                                                                         \n";
    $stSql .= "    , (   SELECT cod_assentamento                                                                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento                                                                              \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento                                                                                      \n";
    $stSql .= "WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                           \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                         \n";
    $stSql .= "  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                     \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                  \n";
    $stSql .= "  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                     \n";
    $stSql .= "  AND assentamento.timestamp = max_assentamento.timestamp                                                                                   \n";
    $stSql .= "  AND NOT EXISTS (SELECT *                                                                                                                  \n";
    $stSql .= "                    FROM pessoal.assentamento_gerado_excluido                                                      \n";
    $stSql .= "                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                 \n";
    $stSql .= "                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                            \n";

    return $stSql;
}

function recuperaAssentamentoGeradoSemEvento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaAssentamentoGeradoSemEvento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoGeradoSemEvento()
{
    $stSql .= "SELECT assentamento_gerado.*                                                                                                                \n";
    $stSql .= " FROM pessoal.assentamento_gerado_contrato_servidor                                                                                         \n";
    $stSql .= "    , pessoal.assentamento_gerado                                                                                                           \n";
    $stSql .= "    , (   SELECT cod_assentamento_gerado                                                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento_gerado                                                                                                \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                        \n";
    $stSql .= "    , pessoal.assentamento                                                                                                                  \n";
    $stSql .= "    , (   SELECT cod_assentamento                                                                                                           \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                \n";
    $stSql .= "            FROM pessoal.assentamento                                                                                                       \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento                                                                                      \n";
    $stSql .= "WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                           \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                         \n";
    $stSql .= "  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                                     \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                                  \n";
    $stSql .= "  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                                     \n";
    $stSql .= "  AND assentamento.timestamp = max_assentamento.timestamp                                                                                   \n";
    $stSql .= "  AND NOT EXISTS (SELECT *                                                                                                                  \n";
    $stSql .= "                    FROM pessoal.assentamento_gerado_excluido                                                                               \n";
    $stSql .= "                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                 \n";
    $stSql .= "                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                            \n";

    return $stSql;
}

function excluirAssentamentoGerado($stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirAssentamentoGerado($stFiltro);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirAssentamentoGerado($stFiltro)
{
    $stSql  = "DELETE FROM pessoal.assentamento_gerado WHERE cod_assentamento_gerado IN (SELECT cod_assentamento_gerado                        \n";
    $stSql .= "                                                                            FROM pessoal.assentamento_gerado_contrato_servidor  \n";
    $stSql .= "                                                                           ".$stFiltro.")                                       \n";

    return $stSql;
}

}