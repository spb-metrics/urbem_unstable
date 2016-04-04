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
    * Classe de mapeamento da tabela FN_EMPENHO_SITUACAO_EMPENHO
    * Data de Criação: 12/05/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.13
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoSituacaoEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoSituacaoEmpenho()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_situacao_empenho');

    $this->AddCampo('empenho'       ,'integer',false,''    ,false,false);
    $this->AddCampo('entidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('emissao'       ,'text',false,''       ,false,false);
    $this->AddCampo('credor'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('empenhado'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('anulado'       ,'numeric',false,'14.2',false,false);
    $this->AddCampo('liquidado'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('pago'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('data_pagamento','text',false,''       ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select retorno.*                                                                                        \n";
    $stSql .= "  from ".$this->getTabela()."('".$this->getDado("stEntidade")."',                                        \n";
    $stSql .= "  '".$this->getDado("exercicio")."','".$this->getDado("stDataInicialEmissao")."',                        \n";
    $stSql .= "  '".$this->getDado("stDataFinalEmissao")."','".$this->getDado("stDataInicialAnulacao")."',              \n";
    $stSql .= "  '".$this->getDado("stDataFinalAnulacao")."','".$this->getDado("stDataInicialLiquidacao")."',           \n";
    $stSql .= "  '".$this->getDado("stDataFinalLiquidacao")."','".$this->getDado("stDataInicialEstornoLiquidacao")."',  \n";
    $stSql .= "  '".$this->getDado("stDataFinalEstornoLiquidacao")."','".$this->getDado("stDataInicialPagamento")."',   \n";
    $stSql .= "  '".$this->getDado("stDataFinalPagamento")."','" . $this->getDado("stDataInicialEstornoPagamento")."',  \n";
    $stSql .= "  '".$this->getDado("stDataFinalEstornoPagamento")."','".$this->getDado("inCodEmpenhoInicial")."',       \n";
    $stSql .= "  '".$this->getDado("inCodEmpenhoFinal")."','".$this->getDado("inCodDotacao")."',                        \n";
    $stSql .= "  '".$this->getDado("inCodDespesa")."','".$this->getDado("inCodRecurso")."',                             \n";
    $stSql .= "  '".$this->getDado("stDestinacaoRecurso")."','".$this->getDado("inCodDetalhamento")."',                 \n";
    $stSql .= "  '".$this->getDado("inNumOrgao")."','".$this->getDado("inNumUnidade")."',                               \n";
    $stSql .= "  '".$this->getDado("inOrdenacao")."','".$this->getDado("inCodFornecedor")."',                           \n";
    $stSql .= "  '".$this->getDado("inSituacao")."', '".$this->getDado("stTipoEmpenho")."') as retorno(                 \n";
    $stSql .= "  empenho             integer,                                           \n";
    $stSql .= "  entidade            integer,                                           \n";
    $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  emissao             text,                                              \n";
    $stSql .= "  credor              varchar,                                           \n";
    $stSql .= "  empenhado           numeric,                                           \n";
    $stSql .= "  anulado             numeric,                                           \n";
    $stSql .= "  saldoempenhado      numeric,                                           \n";
    $stSql .= "  liquidado           numeric,                                           \n";
    $stSql .= "  pago                numeric,                                           \n";
    $stSql .= "  aliquidar           numeric,                                           \n";
    $stSql .= "  empenhadoapagar     numeric,                                           \n";
    $stSql .= "  liquidadoapagar     numeric,                                           \n";
    $stSql .= "  cod_recurso         integer                                            \n";
    $stSql .= "  )                                                                        ";

    if (Sessao::getExercicio() > '2015') {
        $stSql .= "
                    INNER JOIN empenho.empenho
                            ON empenho.cod_empenho = retorno.empenho
                           AND empenho.cod_entidade = retorno.entidade
                           AND empenho.exercicio = retorno.exercicio

                    INNER JOIN empenho.pre_empenho
                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    INNER JOIN empenho.item_pre_empenho
                            ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                           AND item_pre_empenho.exercicio = pre_empenho.exercicio
                \n";

        if ($this->getDado("inCentroCusto") != "") {
            $stSql .= " WHERE item_pre_empenho.cod_centro = ".$this->getDado("inCentroCusto")."\n";
        }
    }

    return $stSql;
}

function geraRecuperaTodosPorRecurso()
{
}
function montaRecuperaTodosPorRecurso()
{
}
}
