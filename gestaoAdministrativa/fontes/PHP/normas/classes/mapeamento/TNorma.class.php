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
* Classe de Mapeamento para tabela norma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 22985 $
$Name$
$Author: andre.almeida $
$Date: 2007-05-30 18:21:17 -0300 (Qua, 30 Mai 2007) $

Casos de uso: uc-01.04.02, uc-02.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
 * Efetua conexão com a tabela  SW_NORMA
 * Data de Criação: 26/05/2004

 * @author Analista: Leandro Oliveira
 * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TNorma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TNorma()
{
    parent::Persistente();
    $this->setTabela('normas.norma');

    $this->setCampoCod('cod_norma');
    $this->setComplementoChave('');

    $this->AddCampo('cod_norma'     ,'integer' ,true ,''   ,true  ,false);
    $this->AddCampo('cod_tipo_norma','integer' ,true ,''   ,false ,true );
    $this->AddCampo('dt_publicacao' ,'date'    ,true ,''   ,false ,false);
    $this->AddCampo('nom_norma'     ,'varchar' ,true ,'40' ,false ,false);
    $this->AddCampo('descricao'     ,'text'    ,true ,''   ,false ,false);
    $this->AddCampo('link'          ,'varchar' ,true ,'80' ,false ,false);
    $this->AddCampo('exercicio'     ,'varchar' ,true ,'4'  ,false ,false);
    $this->AddCampo('num_norma'     ,'varchar' ,true ,''   ,true  ,false);
    $this->AddCampo('dt_assinatura' ,'date'    ,true ,''   ,false ,true );
}

function recuperaNormas(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY N.num_norma,N.exercicio ";
    $stSql  = $this->montaRecuperaNormas().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNormas()
{
    $stSQL  = " SELECT                                                                    \n";
    $stSQL .= "  cod_norma                                                                \n";
    $stSQL .= " ,N.cod_tipo_norma                                                           \n";
    $stSQL .= " ,to_char( dt_publicacao::date, 'dd/mm/yyyy'::text )  as dt_publicacao                 \n";
    $stSQL .= " ,nom_norma                                                                \n";
    $stSQL .= " ,descricao                                                                \n";
    $stSQL .= " ,exercicio                                                                \n";
    $stSQL .= " ,dt_assinatura                                                            \n";
    $stSQL .= " ,to_char( dt_assinatura::date, 'dd/mm/yyyy'::text )  as dt_assinatura_formatado       \n";
    $stSQL .= " ,lpad(num_norma,6,'0') as num_norma                              \n";
    $stSQL .= " ,link                                                                     \n";
    $stSQL .= " , (lpad(num_norma,6,'0')||'/'||exercicio)  as num_norma_exercicio  \n";
    $stSQL .= " FROM                                                                      \n";
    $stSQL .= "     normas.norma AS N                                                     \n";

   return $stSQL;

}

function recuperaNormasVigenteOrRevogado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY N.num_norma,N.exercicio ";
    $stSql  = $this->montaRecuperaNormasVigenteOrRevogado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNormasVigenteOrRevogado()
{
    $stSQL .= "  SELECT dt_termino                                                                      \n";
    $stSQL .= "       , cod_norma                                                                       \n";
    $stSQL .= "       , N.cod_tipo_norma                                                                \n";
    $stSQL .= "       , to_char( dt_publicacao::date, 'dd/mm/yyyy'::text ) AS dt_publicacao             \n";
    $stSQL .= "       , nom_norma                                                                       \n";
    $stSQL .= "       , initcap( descricao ) AS descricao                                               \n";
    $stSQL .= "       , exercicio                                                                       \n";
    $stSQL .= "       , dt_assinatura                                                                   \n";
    $stSQL .= "       , to_char( dt_assinatura::date, 'dd/mm/yyyy'::text ) AS dt_assinatura_formatado   \n";
    $stSQL .= "       , lpad(num_norma,6,'0') as num_norma                                              \n";
    $stSQL .= "       , link                                                                            \n";
    $stSQL .= "       , (lpad(num_norma,6,'0')||'/'||exercicio) AS num_norma_exercicio                  \n";
    $stSQL .= "  FROM                                                                                   \n";
    $stSQL .= "      normas.norma AS N NATURAL                                                          \n";
    $stSQL .= " JOIN normas.norma_data_termino AS V                                                     \n";

   return $stSQL;

}

function recuperaNormasDecreto(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY N.num_norma,N.exercicio ";
    $stSql  = $this->montaRecuperaNormasDecreto().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNormasDecreto()
{
    $stSQL = " SELECT                                                                        \n";
    $stSQL .= "  N.cod_norma                                                                  \n";
    $stSQL .= " ,N.cod_tipo_norma                                                             \n";
    $stSQL .= " ,TN.nom_tipo_norma                                                            \n";
    $stSQL .= " ,to_char( N.dt_publicacao, 'dd/mm/yyyy' )  as dt_publicacao                   \n";
    $stSQL .= " ,to_char( N.dt_assinatura, 'dd/mm/yyyy' )  as dt_assinatura                   \n";
    $stSQL .= " ,N.nom_norma                                                                  \n";
    $stSQL .= " ,N.descricao                                                                  \n";
    $stSQL .= " ,N.exercicio                                                                  \n";
    $stSQL .= " ,lpad(num_norma,6,'0') as num_norma                                \n";
    $stSQL .= " ,link                                                                         \n";
    $stSQL .= " , ( lpad(num_norma,6,'0')||'/'||N.exercicio ) as num_norma_exercicio  \n";
    $stSQL .= " FROM                                                                          \n";
    $stSQL .= "     normas.norma AS N                                                         \n";
    $stSQL .= " LEFT JOIN                                                                     \n";
    $stSQL .= "     normas.tipo_norma AS TN                                                   \n";
    $stSQL .= " ON  TN.cod_tipo_norma = N.cod_tipo_norma                                      \n";

   return $stSQL;

}

function recuperaDadosExportacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY exercicio ";
    $stSql  = $this->montaRecuperaDadosExportacao().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaDadosExportacao()
{
    $stSQL .= "SELECT                                                                                                       \n";
    $stSQL .= "     numero_da_lei,                                                                                          \n";
    $stSQL .= "     substr(data_da_lei,7,8) as exercicio_da_lei,                                                            \n";
    $stSQL .= "     data_da_lei,                                                                                            \n";
    $stSQL .= "     num_norma as cod_norma,                                                                                 \n";
    $stSQL .= "     exercicio,                                                                                              \n";
    $stSQL .= "     dt_publicacao,                                                                                          \n";
    $stSQL .= "     valor_credito_adicional,                                                                                \n";
    $stSQL .= "     valor_reducao_dotacoes,                                                                                 \n";
    $stSQL .= "     tipo_credito_adicional,                                                                                 \n";
    $stSQL .= "     origem_do_recurso                                                                                       \n";
    $stSQL .= "FROM (                                                                                                       \n";
    $stSQL .= "     SELECT DISTINCT                                                                                         \n";
    $stSQL .= "        suplementacao.cod_suplementacao,                                                                                \n";
    $stSQL .= "        norma.num_norma,                                                                                        \n";
    $stSQL .= "        norma.exercicio,                                                                                        \n";
    $stSQL .= "        to_char(norma.dt_publicacao,'ddmmyyyy')as dt_publicacao,                                                \n";
    $stSQL .= "        CASE WHEN norma.cod_tipo_norma=1 THEN                                                                   \n";
    $stSQL .= "            tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Número da Lei')                 \n";
    $stSQL .= "            ELSE                                                                                             \n";
    $stSQL .= "            CAST(norma.num_norma as varchar)                                                                    \n";
    $stSQL .= "        END as numero_da_lei,                                                                                \n";
    $stSQL .= "        CASE WHEN norma.cod_tipo_norma=1 THEN                                                                   \n";
    $stSQL .= "            tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Data da Lei')                   \n";
    $stSQL .= "            ELSE                                                                                             \n";
    $stSQL .= "            to_char(norma.dt_publicacao,'dd/mm/yyyy')                                                           \n";
    $stSQL .= "        END as data_da_lei,                                                                                  \n";
    $stSQL .= "        tcers.fn_total_valor_credito('".$this->getDado("stExercicio")."',suplementacao.cod_suplementacao, '".$this->getDado("stCodEntidades")."') as valor_credito_adicional,    \n";
    $stSQL .= "        tcers.fn_total_valor_reducao('".$this->getDado("stExercicio")."',suplementacao.cod_suplementacao, '".$this->getDado("stCodEntidades")."') as valor_reducao_dotacoes,     \n";
    $stSQL .= "        CASE                                                                                                 \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (1,2,3,4,5,13,14,15)   THEN 1                                            \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (6,7,8,9,10)        THEN 2                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (11)                THEN 3                                               \n";
    $stSQL .= "        END AS tipo_credito_adicional,                                                                       \n";
    $stSQL .= "        CASE                                                                                                 \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (5,10)              THEN 1                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (4,9)               THEN 2                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (2,7)               THEN 3                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (3,8)               THEN 4                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (1,6,13)            THEN 5                                               \n";
    $stSQL .= "                WHEN suplementacao.cod_tipo IN (14,15)             THEN 6                                               \n";
    $stSQL .= "        END AS origem_do_recurso                                                                             \n";
    $stSQL .= "     FROM                                                                                                    \n";
    $stSQL .= "        normas.norma                                ,                                                   \n";
    $stSQL .= "        orcamento.suplementacao,                                                                        \n";
    $stSQL .= "        contabilidade.transferencia_despesa  ,                                                          \n";
    $stSQL .= "        contabilidade.lote                                                                            \n";
    $stSQL .= "     WHERE                                                                                                   \n";
//  $stSQL .= "            norma.exercicio = '".$this->getDado("stExercicio")."'                                               \n";
    $stSQL .= "               transferencia_despesa.cod_suplementacao     = suplementacao.cod_suplementacao                                                  \n";
    $stSQL .= "        AND transferencia_despesa.exercicio             = suplementacao.exercicio                                                          \n";
    $stSQL .= "        AND transferencia_despesa.cod_lote              = lote.cod_lote                                                           \n";
    $stSQL .= "        AND suplementacao.cod_tipo              not in (12,16)                                                                    \n";
    $stSQL .= "        AND transferencia_despesa.exercicio             = lote.exercicio                                                          \n";
    $stSQL .= "        AND transferencia_despesa.tipo                  = lote.tipo                                                               \n";
    $stSQL .= "        AND transferencia_despesa.cod_entidade          = lote.cod_entidade                                                       \n";
    $stSQL .= "        AND lote.dt_lote between to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')\n";
    $stSQL .= "        AND suplementacao.dt_suplementacao between to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')\n";
    $stSQL .= "        AND transferencia_despesa.cod_entidade          IN (".$this->getDado("stCodEntidades").")                               \n";
    $stSQL .= "        AND norma.cod_norma             = suplementacao.cod_norma                                                          \n";
    $stSQL .= "        AND suplementacao.cod_suplementacao || suplementacao.exercicio NOT IN (                                                    \n";
    $stSQL .= "           SELECT                                                                                            \n";
    $stSQL .= "              cod_suplementacao || exercicio                                                                 \n";
    $stSQL .= "           FROM                                                                                              \n";
    $stSQL .= "              orcamento.suplementacao_anulada                                                                \n";
    $stSQL .= "           WHERE                                                                                             \n";
    $stSQL .= "              exercicio   = '".$this->getDado("stExercicio")."'                                              \n";
    $stSQL .= "        )                                                                                                    \n";
    $stSQL .= "        AND suplementacao.cod_suplementacao || suplementacao.exercicio NOT IN (                                                    \n";
    $stSQL .= "           SELECT                                                                                            \n";
    $stSQL .= "             cod_suplementacao_anulacao || exercicio                                                         \n";
    $stSQL .= "           FROM                                                                                              \n";
    $stSQL .= "             orcamento.suplementacao_anulada                                                                 \n";
    $stSQL .= "           WHERE                                                                                             \n";
    $stSQL .= "             exercicio   = '".$this->getDado("stExercicio")."'                                               \n";
    $stSQL .= "        )                                                                                                    \n";
    $stSQL .= ") as tabela                                                                                                  \n";

    return $stSQL;
}

function recuperaDadosExportacaoLPP(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaDadosExportacaoLPP",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDadosExportacaoLPP()
{
    $stSql = "
                SELECT  ppa_alteracao.nroleialteracao AS nroleialteracao
                      , TO_CHAR(ppa_alteracao.dataleialteracao, 'DDMMYYYY') AS dataleialteracao
                      , TO_CHAR(ppa_alteracao.datapubleialt, 'DDMMYYYY') AS datapubleialt
                      , ppa_anterior.nroLeiPPA
                      , TO_CHAR(ppa_anterior.dataLeiPPA, 'DDMMYYYY') AS dataLeiPPA
                      , TO_CHAR(ppa_anterior.dataPubLeiPPA, 'DDMMYYYY') AS dataPubLeiPPA
                FROM tcemg.configuracao_leis_ppa
                JOIN normas.norma
                  ON norma.cod_norma = configuracao_leis_ppa.cod_norma

                JOIN(  SELECT num_norma AS nroLeiPPA
                             , dt_assinatura AS dataLeiPPA
                             , dt_publicacao AS dataPubLeiPPA
                             , configuracao_leis_ppa.exercicio
              FROM tcemg.configuracao_leis_ppa
              JOIN normas.norma
                            ON norma.cod_norma = configuracao_leis_ppa.cod_norma
                         WHERE  configuracao_leis_ppa.status <> 'f'
                           AND configuracao_leis_ppa.tipo_configuracao = 'consulta'
                    ) as ppa_anterior
                   ON ppa_anterior.exercicio = configuracao_leis_ppa.exercicio
                  AND ppa_anterior.dataLeiPPA = norma.dt_assinatura
                  AND ppa_anterior.dataPubLeiPPA = norma.dt_publicacao
                  AND ppa_anterior.nroLeiPPA = norma.num_norma

           LEFT JOIN(   SELECT norma.num_norma AS nroleialteracao
                             , norma.dt_assinatura AS dataleialteracao
                             , norma.dt_publicacao AS datapubleialt
                             , configuracao_leis_ppa.exercicio
                          FROM tcemg.configuracao_leis_ppa
              JOIN normas.norma
                            ON norma.cod_norma = configuracao_leis_ppa.cod_norma
                         WHERE configuracao_leis_ppa.tipo_configuracao = 'alteracao'
                           AND configuracao_leis_ppa.status <> 'f'
                    ) as ppa_alteracao
                   ON ppa_anterior.exercicio = configuracao_leis_ppa.exercicio
                WHERE configuracao_leis_ppa.exercicio = '".$this->getDado('exercicio')."'
                  AND configuracao_leis_ppa.status <> 'f'
    ";

    return $stSql;
}

function recuperaDadosExportacaoLDO(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaDadosExportacaoLDO().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacaoLDO()
{
    $stSql = "
                SELECT  ldo_alteracao.numero_ldo AS numero_lei_alteracao
                      , TO_CHAR(ldo_alteracao.data_ldo, 'DDMMYYYY') AS data_lei_alteracao
                      , TO_CHAR(ldo_alteracao.data_publicacao_ldo, 'DDMMYYYY') AS data_publicacao_lei_alteracao
                      , ldo_anterior.numero_ldo
                      , TO_CHAR(ldo_anterior.data_ldo, 'DDMMYYYY') AS data_ldo
                      , TO_CHAR(ldo_anterior.data_publicacao_ldo, 'DDMMYYYY') AS data_publicacao_ldo
                FROM tcemg.configuracao_leis_ldo

                JOIN normas.norma
                  ON norma.cod_norma = configuracao_leis_ldo.cod_norma

           LEFT JOIN (SELECT num_norma AS numero_ldo
                           , dt_assinatura AS data_ldo
                           , dt_publicacao AS data_publicacao_ldo
                           , configuracao_leis_ldo.exercicio
                        FROM tcemg.configuracao_leis_ldo
                        JOIN normas.norma
                          ON norma.cod_norma = configuracao_leis_ldo.cod_norma
                     WHERE configuracao_leis_ldo.tipo_configuracao = 'consulta'
                       AND configuracao_leis_ldo.status <> 'f'
                    ) as ldo_anterior
                  ON ldo_anterior.exercicio = configuracao_leis_ldo.exercicio
                 AND ldo_anterior.data_ldo = norma.dt_assinatura
                 AND ldo_anterior.data_publicacao_ldo = norma.dt_publicacao
                 AND ldo_anterior.numero_ldo = norma.num_norma

           LEFT JOIN (SELECT num_norma AS numero_ldo
                           , dt_assinatura AS data_ldo
                           , dt_publicacao AS data_publicacao_ldo
                           , configuracao_leis_ldo.exercicio
                        FROM tcemg.configuracao_leis_ldo
                        JOIN normas.norma
                          ON norma.cod_norma = configuracao_leis_ldo.cod_norma
                     WHERE configuracao_leis_ldo.tipo_configuracao = 'alteracao'
                       AND configuracao_leis_ldo.status <> 'f'
                    ) as ldo_alteracao
                  ON ldo_anterior.exercicio = configuracao_leis_ldo.exercicio
                 AND ldo_alteracao.data_ldo = norma.dt_assinatura
                 AND ldo_alteracao.data_publicacao_ldo = norma.dt_publicacao
                 AND ldo_alteracao.numero_ldo = norma.num_norma

                WHERE configuracao_leis_ldo.exercicio = '".$this->getDado('exercicio')."'
                  AND configuracao_leis_ldo.status <> 'f'
    ";

    return $stSql;
}
}
