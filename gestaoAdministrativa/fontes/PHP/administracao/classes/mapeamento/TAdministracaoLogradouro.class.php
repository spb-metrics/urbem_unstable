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
* Classe de mapeamento para administracao.logradouro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TLogradouro extends Persistente
{
function TLogradouro()
{
    parent::Persistente();
    $this->setTabela('sw_logradouro');
    $this->setCampoCod('cod_logradouro');

    $this->AddCampo('cod_logradouro',      'integer', true, '', true,  false);
    $this->AddCampo('cod_uf',              'integer', true, '', false, true);
    $this->AddCampo('cod_municipio',       'integer', true, '', false, true);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                \n";
    $stSql .= "     TL.cod_tipo,                                      \n";
    $stSql .= "     TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome, \n";
    $stSql .= "     TL.nom_tipo,                                      \n";
    $stSql .= "     NL.nom_logradouro,                                \n";
    $stSql .= "     L.*,                                              \n";
    $stSql .= "     B.nom_bairro,                                     \n";
    $stSql .= "     M.nom_municipio,                                  \n";
    $stSql .= "     U.nom_uf,                                         \n";
    $stSql .= "     U.sigla_uf,                                       \n";
    $stSql .= "     imobiliario.fn_consulta_cep(L.cod_logradouro) AS cep \n";
    $stSql .= " FROM                                                  \n";
    $stSql .= "    sw_tipo_logradouro   AS TL,                       \n";
    $stSql .= "    sw_nome_logradouro   AS NL,                       \n";
    $stSql .= "    sw_municipio         AS M,                        \n";
    $stSql .= "    sw_uf                AS U,                        \n";
    $stSql .= "     ( SELECT                                          \n";
    $stSql .= "           MAX(timestamp) AS timestamp,                \n";
    $stSql .= "           cod_logradouro                              \n";
    $stSql .= "       FROM                                            \n";
    $stSql .= "           sw_nome_logradouro                         \n";
    $stSql .= "       GROUP BY cod_logradouro                         \n";
    $stSql .= "       ORDER BY cod_logradouro                         \n";
    $stSql .= "     ) AS MNL,                                         \n";
    $stSql .= "    sw_logradouro        AS L                         \n";
    $stSql .= " LEFT OUTER JOIN sw_bairro_logradouro    AS BL ON     \n";
    $stSql .= "     BL.cod_logradouro = L.cod_logradouro   AND        \n";
    $stSql .= "     BL.cod_uf         = L.cod_uf           AND        \n";
    $stSql .= "     BL.cod_municipio  = L.cod_municipio               \n";
    $stSql .= " LEFT OUTER JOIN sw_bairro               AS B ON      \n";
    $stSql .= "     B.cod_bairro      = BL.cod_bairro      AND        \n";
    $stSql .= "     B.cod_uf          = BL.cod_uf          AND        \n";
    $stSql .= "     B.cod_municipio   = BL.cod_municipio              \n";
    $stSql .= " WHERE                                                 \n";
    $stSql .= "     L.cod_logradouro  = NL.cod_logradouro  AND        \n";
    $stSql .= "     NL.cod_logradouro = MNL.cod_logradouro AND        \n";
    $stSql .= "     NL.timestamp      = MNL.timestamp      AND        \n";
    $stSql .= "     L.cod_municipio   = M.cod_municipio    AND        \n";
    $stSql .= "     L.cod_uf          = M.cod_uf           AND        \n";
    $stSql .= "     M.cod_uf          = U.cod_uf           AND        \n";
    $stSql .= "     NL.cod_tipo       = TL.cod_tipo                   \n";

    return $stSql;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelacionamentoRelatorio()
{
    $stSql  = "SELECT *                                                         \n";
    $stSql .= "\t FROM (                                                             \n";
    $stSql .= "\t    SELECT                                                     \n";
    $stSql .= "\t        TL.cod_tipo,                                           \n";
    $stSql .= "\t        TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome,      \n";
    $stSql .= "\t        TL.nom_tipo,                                           \n";
    $stSql .= "\t        NL.nom_logradouro,                                     \n";
    $stSql .= "\t        L.*,                                                   \n";
    $stSql .= "\t        B.cod_bairro,                                          \n";
    $stSql .= "\t        B.nom_bairro,                                          \n";
    $stSql .= "\t        M.nom_municipio,                                       \n";
//   $stSql .= "\t        U.cod_uf,                                              \n";
    $stSql .= "\t        U.nom_uf,                                              \n";
    $stSql .= "\t        U.sigla_uf,                                            \n";
    $stSql .= "\t        imobiliario.fn_consulta_cep(L.cod_logradouro) AS cep   \n";
    $stSql .= "\t    FROM                                                       \n";
    $stSql .= "\t       sw_tipo_logradouro   AS TL,                            \n";
    $stSql .= "\t       sw_nome_logradouro   AS NL,                            \n";
    $stSql .= "\t       sw_municipio         AS M,                             \n";
    $stSql .= "\t       sw_uf                AS U,                             \n";
    $stSql .= "\t        ( SELECT                                               \n";
    $stSql .= "\t              MAX(timestamp) AS timestamp,                     \n";
    $stSql .= "\t              cod_logradouro                                   \n";
    $stSql .= "\t          FROM                                                 \n";
    $stSql .= "\t              sw_nome_logradouro                              \n";
    $stSql .= "\t          GROUP BY cod_logradouro                              \n";
    $stSql .= "\t          ORDER BY cod_logradouro                              \n";
    $stSql .= "\t        ) AS MNL,                                              \n";
    $stSql .= "\t       sw_logradouro        AS L                              \n";
    $stSql .= "\t    LEFT OUTER JOIN sw_bairro_logradouro    AS BL ON          \n";
    $stSql .= "\t        BL.cod_logradouro = L.cod_logradouro   AND             \n";
    $stSql .= "\t        BL.cod_uf         = L.cod_uf           AND             \n";
    $stSql .= "\t        BL.cod_municipio  = L.cod_municipio                    \n";
    $stSql .= "\t    LEFT OUTER JOIN sw_bairro               AS B ON           \n";
    $stSql .= "\t        B.cod_bairro      = BL.cod_bairro      AND             \n";
    $stSql .= "\t        B.cod_uf          = BL.cod_uf          AND             \n";
    $stSql .= "\t        B.cod_municipio   = BL.cod_municipio                   \n";
    $stSql .= "\t    WHERE                                                      \n";
    $stSql .= "\t        L.cod_logradouro  = NL.cod_logradouro  AND             \n";
    $stSql .= "\t        NL.cod_logradouro = MNL.cod_logradouro AND             \n";
    $stSql .= "\t        NL.timestamp      = MNL.timestamp      AND             \n";
    $stSql .= "\t        L.cod_municipio   = M.cod_municipio    AND             \n";
    $stSql .= "\t        L.cod_uf          = M.cod_uf           AND             \n";
    $stSql .= "\t        M.cod_uf          = U.cod_uf           AND             \n";
    $stSql .= "\t        NL.cod_tipo       = TL.cod_tipo                        \n";
    $stSql .= ") as tabela                                                      \n";

    return $stSql;
}

}
