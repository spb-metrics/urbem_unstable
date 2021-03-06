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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONSTRUCAO_EDIFICACAO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMConstrucaoEdificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONSTRUCAO_EDIFICACAO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMConstrucaoEdificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMConstrucaoEdificacao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.construcao_edificacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo,cod_construcao');

    $this->AddCampo('cod_tipo','integer',true,'',true,true          );
    $this->AddCampo('cod_construcao','integer',true,'',true,true    );

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                 \n";
    $stSql  = "     CT.COD_CONSTRUCAO,                                                 \n";
    $stSql  = "     CED.COD_TIPO,                                                      \n";
    $stSql  = "     TE.NOM_TIPO,                                                       \n";
    $stSql  = "     CT.DESCRICAO,                                                      \n";
    $stSql  = "     AC.AREA_REAL,                                                      \n";
    $stSql  = "     CP.COD_PROCESSO,                                                   \n";
    $stSql  = "     CP.EXERCICIO,                                                      \n";
    $stSql  = "     CASE                                                               \n";
    $stSql  = "         WHEN                                                           \n";
    $stSql  = "             CAST( UD.INSCRICAO_MUNICIPAL AS VARCHAR ) IS NOT NULL      \n";
    $stSql  = "         THEN                                                           \n";
    $stSql  = "             CAST( UD.INSCRICAO_MUNICIPAL AS VARCHAR )                  \n";
    $stSql  = "         WHEN                                                           \n";
    $stSql  = "             CAST( UA.INSCRICAO_MUNICIPAL AS VARCHAR ) IS NOT NULL      \n";
    $stSql  = "         THEN                                                           \n";
    $stSql  = "             CAST( UA.INSCRICAO_MUNICIPAL AS VARCHAR )                  \n";
    $stSql  = "         ELSE                                                           \n";
    $stSql  = "             CD.NOM_CONDOMINIO                                          \n";
    $stSql  = "     END AS IMOVEL_COND,                                                \n";
    $stSql  = "     UA.NUMERO,                                                         \n";
    $stSql  = "     UA.COMPLEMENTO,                                                    \n";
    $stSql  = "     CASE                                                               \n";
    $stSql  = "         WHEN                                                           \n";
    $stSql  = "             CAST( AUD.AREA AS VARCHAR ) IS NOT NULL                    \n";
    $stSql  = "         THEN                                                           \n";
    $stSql  = "             CAST( AUD.AREA AS VARCHAR )                                \n";
    $stSql  = "         ELSE                                                           \n";
    $stSql  = "             CAST( AUA.AREA AS VARCHAR )                                \n";
    $stSql  = "     END AS AREA_UNIDADE,                                               \n";
    $stSql  = "     CASE                                                               \n";
    $stSql  = "         WHEN                                                           \n";
    $stSql  = "             CAST( UD.INSCRICAO_MUNICIPAL AS VARCHAR ) IS NOT NULL      \n";
    $stSql  = "         THEN                                                           \n";
    $stSql  = "             'Dependente'                                               \n";
    $stSql  = "         WHEN                                                           \n";
    $stSql  = "             CAST( UA.INSCRICAO_MUNICIPAL AS VARCHAR ) IS NOT NULL      \n";
    $stSql  = "         THEN                                                           \n";
    $stSql  = "             'Autônoma'                                                 \n";
    $stSql  = "         ELSE                                                           \n";
    $stSql  = "             'Condomínio'                                               \n";
    $stSql  = "     END AS TIPO_VINCULO                                                \n";
    $stSql  = " FROM                                                                   \n";
    $stSql  = "     imobiliario.construcao_outros AS CT                                    \n";
    $stSql  = " INNER JOIN                                                             \n";
    $stSql  = "      (                                                                 \n";
    $stSql  = "      SELECT                                                            \n";
    $stSql  = "         AC.*                                                           \n";
    $stSql  = "      FROM                                                              \n";
    $stSql  = "         imobiliario.area_construcao AS AC,                                 \n";
    $stSql  = "         (                                                              \n";
    $stSql  = "         SELECT                                                         \n";
    $stSql  = "             MAX (TIMESTAMP) AS TIMESTAMP,                              \n";
    $stSql  = "             COD_CONSTRUCAO                                             \n";
    $stSql  = "          FROM                                                          \n";
    $stSql  = "             imobiliario.area_construcao                                    \n";
    $stSql  = "          GROUP BY                                                      \n";
    $stSql  = "             COD_CONSTRUCAO                                             \n";
    $stSql  = "      ) AS MAC                                                          \n";
    $stSql  = "      WHERE                                                             \n";
    $stSql  = "         AC.COD_CONSTRUCAO = MAC.COD_CONSTRUCAO                         \n";
    $stSql  = "         AND AC.TIMESTAMP = MAC.TIMESTAMP) AS AC                        \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = AC.COD_CONSTRUCAO                              \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     imobiliario.construcao_edificacao AS CED                               \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CED.COD_CONSTRUCAO = CT.COD_CONSTRUCAO                             \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     imobiliario.tipo_edificacao AS TE                                      \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     TE.COD_TIPO = CED.COD_TIPO                                         \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     imobiliario.construcao_processo AS CP                                  \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = CP.COD_CONSTRUCAO                              \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "    imobiliario.unidade_dependente AS UD                                    \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = UD.COD_CONSTRUCAO_DEPENDENTE                   \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     imobiliario.unidade_autonoma AS UA                                     \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = UA.COD_CONSTRUCAO                              \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     (                                                                  \n";
    $stSql  = "     SELECT                                                             \n";
    $stSql  = "         AUA.*                                                          \n";
    $stSql  = "     FROM                                                               \n";
    $stSql  = "         imobiliario.area_unidade_autonoma AS AUA,                          \n";
    $stSql  = "             (                                                          \n";
    $stSql  = "             SELECT                                                     \n";
    $stSql  = "                 MAX (TIMESTAMP) AS TIMESTAMP,                          \n";
    $stSql  = "                 COD_CONSTRUCAO                                         \n";
    $stSql  = "             FROM                                                       \n";
    $stSql  = "                 imobiliario.area_unidade_autonoma                          \n";
    $stSql  = "             GROUP BY                                                   \n";
    $stSql  = "                 COD_CONSTRUCAO                                         \n";
    $stSql  = "             ) AS MAUA                                                  \n";
    $stSql  = "     WHERE                                                              \n";
    $stSql  = "         AUA.COD_CONSTRUCAO = MAUA.COD_CONSTRUCAO                       \n";
    $stSql  = "         AND AUA.TIMESTAMP = MAUA.TIMESTAMP                             \n";
    $stSql  = "     ) AS AUA                                                           \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = AUA.COD_CONSTRUCAO                             \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     (                                                                  \n";
    $stSql  = "     SELECT                                                             \n";
    $stSql  = "         AUD.*                                                          \n";
    $stSql  = "     FROM                                                               \n";
    $stSql  = "         imobiliario.area_unidade_dependente AS AUD,                        \n";
    $stSql  = "             (                                                          \n";
    $stSql  = "             SELECT                                                     \n";
    $stSql  = "                 MAX (TIMESTAMP) AS TIMESTAMP,                          \n";
    $stSql  = "                 COD_CONSTRUCAO_DEPENDENTE                              \n";
    $stSql  = "             FROM                                                       \n";
    $stSql  = "                 imobiliario.area_unidade_dependente                        \n";
    $stSql  = "             GROUP BY                                                   \n";
    $stSql  = "                 COD_CONSTRUCAO_DEPENDENTE                              \n";
    $stSql  = "             ) AS MAUD                                                  \n";
    $stSql  = "     WHERE                                                              \n";
    $stSql  = "         AUD.COD_CONSTRUCAO_DEPENDENTE = MAUD.COD_CONSTRUCAO_DEPENDENTE \n";
    $stSql  = "         AND AUD.TIMESTAMP = MAUD.TIMESTAMP                             \n";
    $stSql  = "     ) AS AUD                                                           \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = AUD.COD_CONSTRUCAO_DEPENDENTE                  \n";
    $stSql  = " LEFT JOIN                                                              \n";
    $stSql  = "     (                                                                  \n";
    $stSql  = "     SELECT                                                             \n";
    $stSql  = "         CT.COD_CONSTRUCAO,                                             \n";
    $stSql  = "         CD.*                                                           \n";
    $stSql  = "     FROM                                                               \n";
    $stSql  = "         imobiliario.construcao_outros AS CT,                               \n";
    $stSql  = "         imobiliario.construcao_condominio AS CC,                           \n";
    $stSql  = "         imobiliario.condominio AS CD                                       \n";
    $stSql  = "     WHERE                                                              \n";
    $stSql  = "         CT.COD_CONSTRUCAO = CC.COD_CONSTRUCAO AND                      \n";
    $stSql  = "         CD.COD_CONDOMINIO = CC.COD_CONDOMINIO                          \n";
    $stSql  = "     ) AS CD                                                            \n";
    $stSql  = " ON                                                                     \n";
    $stSql  = "     CT.COD_CONSTRUCAO = CD.COD_CONSTRUCAO                              \n";

    return $stSql;
}

function recuperaRelacionamentoProcesso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoProcesso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoProcesso()
{
    $stSQL .= " SELECT                                                                                       \n";
    $stSQL .= "     cp.cod_construcao as cod_construcao,                                                     \n";
    $stSQL .= "     cp.cod_processo as cod_processo,                                                         \n";
    $stSQL .= "     cp.exercicio as ano_exercicio,                                                           \n";
    $stSQL .= "     lpad(cp.cod_processo::varchar,5,'0') || '/' || cp.exercicio as cod_processo_ano,                  \n";
    $stSQL .= "     cp.timestamp as timestamp,                                                               \n";
    $stSQL .= "     to_char(cp.timestamp,'dd/mm/yyyy') as data,                                              \n";
    $stSQL .= "     to_char(cp.timestamp,'hh24:mi:ss') as hora,                                              \n";
    $stSQL .= "     CASE                                                                                     \n";
    $stSQL .= "         WHEN                                                                                 \n";
    $stSQL .= "             aud.area IS NOT NULL                                                             \n";
    $stSQL .= "         THEN                                                                                 \n";
    $stSQL .= "             aud.area                                                                         \n";
    $stSQL .= "         ELSE                                                                                 \n";
    $stSQL .= "             aua.area                                                                         \n";
    $stSQL .= "     END AS area                                                                              \n";
    $stSQL .= " FROM                                                                                         \n";
    $stSQL .= "     imobiliario.construcao_processo AS cp                                                        \n";
    $stSQL .= "     LEFT JOIN imobiliario.area_unidade_dependente AS aud ON                                  \n";
    $stSQL .= "             cp.cod_construcao = aud.cod_construcao_dependente                                \n";
    $stSQL .= "         AND cp.timestamp      = aud.timestamp                                                \n";
    $stSQL .= "     LEFT JOIN imobiliario.area_unidade_autonoma AS aua ON                                    \n";
    $stSQL .= "             cp.cod_construcao = aua.cod_construcao                                           \n";
    $stSQL .= "         AND cp.timestamp      = aua.timestamp                                                \n";

    return $stSQL;
}

function recuperaTimestampConstrucao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTimestampConstrucao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTimestampConstrucao()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     timestamp as timestamp_construcao                       \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     imobiliario.construcao AS cp                            \n";

    return $stSQL;
}

function recuperaAreaConstrucaoCondominio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaAreaConstrucaoCondominio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAreaConstrucaoCondominio()
{
    $stSQL .= " SELECT                                                     \n";
    $stSQL .= "     area_real                                              \n";
    $stSQL .= " FROM                                                       \n";
    $stSQL .= "     imobiliario.area_construcao AC                         \n";

    return $stSQL;
}

function listaDadosEdificacaoImovel(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaListaDadosEdificacaoImovel().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaDadosEdificacaoImovel()
{
    $stSQL = "
        SELECT
            ite.nom_tipo AS nome_tipo_edificacao,
            ice.cod_construcao,
            ice.cod_tipo,
            COALESCE( aua.area, aud.area ) AS area_edificacao,
            iud.cod_construcao_dependente,
            CASE WHEN aua.area IS NOT NULL THEN
                'autonoma'
            ELSE
                'dependente'
            END AS autodep,
            ii.inscricao_municipal

        FROM
            imobiliario.imovel AS ii

        LEFT JOIN
            (
                SELECT
                    max( unidade_autonoma.timestamp ) AS timestamp,
                    unidade_autonoma.inscricao_municipal,
                    unidade_autonoma.cod_tipo,
                    unidade_autonoma.cod_construcao

                FROM
                    imobiliario.unidade_autonoma

                GROUP BY
                    unidade_autonoma.inscricao_municipal,
                    unidade_autonoma.cod_tipo,
                    unidade_autonoma.cod_construcao
            )AS iua
        ON
            iua.inscricao_municipal = ii.inscricao_municipal

        LEFT JOIN
            (
                SELECT
                    max( area_unidade_autonoma.timestamp ) AS timestamp,
                    area_unidade_autonoma.inscricao_municipal,
                    area_unidade_autonoma.cod_tipo,
                    area_unidade_autonoma.cod_construcao,
                    area_unidade_autonoma.area

                FROM
                    imobiliario.area_unidade_autonoma

                GROUP BY
                    area_unidade_autonoma.inscricao_municipal,
                    area_unidade_autonoma.cod_tipo,
                    area_unidade_autonoma.cod_construcao,
                    area_unidade_autonoma.area
            )AS aua
        ON
            aua.inscricao_municipal = iua.inscricao_municipal
            AND aua.cod_tipo = iua.cod_tipo
            AND aua.cod_construcao = iua.cod_construcao

        LEFT JOIN
            (
                SELECT
                    max( unidade_dependente.timestamp ) AS timestamp,
                    unidade_dependente.inscricao_municipal,
                    unidade_dependente.cod_tipo,
                    unidade_dependente.cod_construcao_dependente,
                    unidade_dependente.cod_construcao

                FROM
                    imobiliario.unidade_dependente

                GROUP BY
                    unidade_dependente.inscricao_municipal,
                    unidade_dependente.cod_tipo,
                    unidade_dependente.cod_construcao_dependente,
                    unidade_dependente.cod_construcao
            ) AS iud
        ON
            iud.inscricao_municipal = ii.inscricao_municipal

        LEFT JOIN
            (
                SELECT
                    max( area_unidade_dependente.timestamp ) AS timestamp,
                    area_unidade_dependente.inscricao_municipal,
                    area_unidade_dependente.cod_tipo,
                    area_unidade_dependente.cod_construcao_dependente,
                    area_unidade_dependente.cod_construcao,
                    area_unidade_dependente.area

                FROM
                    imobiliario.area_unidade_dependente

                GROUP BY
                    area_unidade_dependente.inscricao_municipal,
                    area_unidade_dependente.cod_tipo,
                    area_unidade_dependente.cod_construcao_dependente,
                    area_unidade_dependente.cod_construcao,
                    area_unidade_dependente.area
            )AS aud
        ON
            aud.inscricao_municipal = iud.inscricao_municipal
            AND aud.cod_tipo = iud.cod_tipo
            AND aud.cod_construcao_dependente = iud.cod_construcao_dependente
            AND aud.cod_construcao = iud.cod_construcao

        INNER JOIN
            imobiliario.construcao_edificacao AS ice
        ON
            ice.cod_construcao = COALESCE( iua.cod_construcao, iud.cod_construcao_dependente )

        INNER JOIN
            imobiliario.tipo_edificacao AS ite
        ON
            ite.cod_tipo = ice.cod_tipo
    ";

    return $stSQL;
}

}
