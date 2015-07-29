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
    * Classe de mapeamento da tabela FN_ORCAMENTO_BALANCETE_DESPESA
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.22
*/

/*
$Log$
Revision 1.10  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBADemonstrativoConsolidadoDespesa extends Persistente 
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    
    parent::Persistente();
    $this->setTabela('tcmba.fn_demonstrativo_consolidado_despesa');

    $this->AddCampo('tipo_registro'             , 'varchar', false, ''    , false, false);
    $this->AddCampo('unidade_gestora'           , 'varchar', false, ''    , false, false);
    $this->AddCampo('competencia'               , 'varchar', false, ''    , false, false);
    $this->AddCampo('num_orgao'                 , 'varchar', false, ''    , false, false);
    $this->AddCampo('num_unidade'               , 'varchar', false, ''    , false, false);
    $this->AddCampo('cod_funcao'                , 'varchar', false, ''    , false, false);
    $this->AddCampo('cod_subfuncao'             , 'varchar', false, ''    , false, false);
    $this->AddCampo('num_programa'              , 'varchar', false, ''    , false, false);
    $this->AddCampo('num_pao'                   , 'varchar', false, ''    , false, false);
    $this->AddCampo('cod_despesa'               , 'integer', false, ''    , false, false);
    $this->AddCampo('elemento_despesa'          , 'varchar', false, ''    , false, false);
    $this->AddCampo('cod_recurso'               , 'varchar', false, '    ', false, false);
    $this->AddCampo('dotacao_fixada'            , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_suplementar'       , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_suplementar_mes'   , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_especial'          , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_especial_mes'      , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_extraordinario'    , 'numeric', false, '14.2', false, false);
    $this->AddCampo('credito_extraordinario_mes', 'numeric', false, '14.2', false, false);
    $this->AddCampo('reducoes'                  , 'numeric', false, '14.2', false, false);
    $this->AddCampo('reducoes_mes'              , 'numeric', false, '14.2', false, false);
    $this->AddCampo('transferencia'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('transferencia_mes'         , 'numeric', false, '14.2', false, false);
    $this->AddCampo('transferencia_anulacao'    , 'numeric', false, '14.2', false, false);
    $this->AddCampo('transferencia_anulacao_mes', 'numeric', false, '14.2', false, false);
    $this->AddCampo('qdd_acrescimo'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('qdd_acrescimo_mes'         , 'numeric', false, '14.2', false, false);
    $this->AddCampo('qdd_decrescimo'            , 'numeric', false, '14.2', false, false);
    $this->AddCampo('qdd_decrescimo_mes'        , 'numeric', false, '14.2', false, false);
    $this->AddCampo('dotacao_atualizada'        , 'numeric', false, '14.2', false, false);
    $this->AddCampo('empenhado_ano'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('empenhado_mes'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('liquidado_ano'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('liquidado_mes'             , 'numeric', false, '14.2', false, false);
    $this->AddCampo('pago_ano'                  , 'numeric', false, '14.2', false, false);
    $this->AddCampo('pago_mes'                  , 'numeric', false, '14.2', false, false);
    $this->AddCampo('saldo_pagar'               , 'numeric', false, '14.2', false, false);
    $this->AddCampo('saldo_disponivel'          , 'numeric', false, '14.2', false, false);
    $this->AddCampo('reservado_tcm'             , 'numeric', false, '14.2', false, false);
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
/*
 * CHAMADA DA PL
 */
    $stSql  = "
        SELECT 
              1  AS tipo_registro
            , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
            , '".$this->getDado('exercicio').$this->getDado('mes')."' AS competencia
            , num_orgao
            , num_unidade
            , cod_funcao
            , cod_subfuncao
            , num_programa
            , num_pao
            , num_acao
            , SUBSTR(REPLACE(classificacao, '.', ''), 1, 8) AS elemento_despesa
            , cod_recurso
            , vl_original AS dotacao_fixada
            , credito_suplementar
            , credito_suplementar_mes
            , credito_especial
            , credito_especial_mes
            , credito_extraordinario
            , credito_extraordinario_mes
            , transferencia
            , transferencia_mes
            , transferencia_anulacao
            , transferencia_anulacao_mes
            , '' AS qdd_acrescimo
            , '' AS qdd_acrescimo_mes
            , '' AS qdd_decrescimo
            , '' AS qdd_decrescimo_mes
            , reducoes AS credito_anulacao
            , reducoes_mes AS credito_anulacao_mes
            , empenhado_ano 
            , empenhado_per AS empenhado_mes
            , liquidado_ano
            , liquidado_per AS liquidado_mes
            , pago_ano
            , pago_per AS pago_mes
            , (empenhado_ano - pago_ano) AS saldo_pagar
            , ((total_creditos - empenhado_ano) + anulado_ano) AS saldo_disponivel
            , total_creditos AS dotacao_atualizada
            , '' AS reservado_tcm
     FROM ".$this->getTabela()." ('".$this->getDado("exercicio")."',' AND od.cod_entidade IN (".$this->getDado("entidades").") ','".$this->getDado("data_inicio")."','".$this->getDado("data_fim")."','".$this->getDado("stCodEstruturalInicial")."','".$this->getDado("stCodEstruturalFinal")."','".$this->getDado("stCodReduzidoInicial")."','".$this->getDado("stCodReduzidoFinal")."','".$this->getDado("stControleDetalhado")."' ,'".$this->getDado("inNumOrgao")."','".$this->getDado("inNumUnidade")."', '".$this->getDado('stVerificaCreateDropTables')."' )
       AS retorno (
                    exercicio                  char(4),
                    cod_despesa                integer,
                    cod_entidade               integer,
                    cod_programa               integer,
                    cod_conta                  integer,
                    num_pao                    integer,
                    num_orgao                  integer,
                    num_unidade                integer,
                    cod_recurso                integer,
                    cod_funcao                 integer,
                    cod_subfuncao              integer,
                    tipo_conta                 varchar,
                    vl_original                numeric,
                    dt_criacao                 date,   
                    classificacao              varchar,
                    descricao                  varchar,
                    num_recurso                varchar,
                    nom_recurso                varchar,
                    nom_orgao                  varchar,                                                                                
                    nom_unidade                varchar,
                    nom_funcao                 varchar,
                    nom_subfuncao              varchar,
                    nom_programa               varchar,
                    nom_pao                    varchar,
                    empenhado_ano              numeric,
                    empenhado_per              numeric,
                    anulado_ano                numeric,
                    anulado_per                numeric,
                    pago_ano                   numeric,
                    pago_per                   numeric,
                    liquidado_ano              numeric,
                    liquidado_per              numeric,
                    saldo_inicial              numeric,
                    suplementacoes             numeric,
                    reducoes                   numeric,
                    reducoes_mes               numeric,
                    transferencia_anulacao     numeric,
                    transferencia_anulacao_mes numeric,
                    total_creditos  	       numeric,
                    credito_suplementar        numeric,
                    credito_especial  	       numeric,
                    credito_extraordinario     numeric,
                    transferencia	           numeric,
                    num_programa 	           varchar,
                    num_acao 		           varchar,
                    credito_suplementar_mes    numeric,                                                                          
                    credito_especial_mes       numeric,
                    credito_extraordinario_mes numeric,
                    transferencia_mes	       numeric
                ) ";
                
    return $stSql;
}

}