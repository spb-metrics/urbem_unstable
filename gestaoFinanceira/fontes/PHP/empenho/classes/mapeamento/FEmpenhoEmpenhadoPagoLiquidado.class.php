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
    * Classe de mapeamento da tabela FN_ORCAMENTO_EMPENHO_EMPENHADO_PAGO_LIQUIDADO
    * Data de Criação: 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FEmpenhoEmpenhadoPagoLiquidado.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 32880 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoEmpenhadoPagoLiquidado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoEmpenhadoPagoLiquidado()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_empenho_empenhado_pago_liquidado');

    $this->AddCampo('entidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('empenho'       ,'integer',false,''    ,false,false);
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cgm'           ,'integer',false,''    ,false,false);
    $this->AddCampo('razao_social'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_nota'      ,'integer',false,''    ,false,false);
    $this->AddCampo('data'          ,'text',false,''       ,false,false);
    $this->AddCampo('ordem'         ,'integer',false,''    ,false,false);
    $this->AddCampo('conta'         ,'integer',false,''    ,false,false);
    $this->AddCampo('nome_conta'    ,'varchar(50)',false,''    ,false,false);
    $this->AddCampo('valor'         ,'numeric',false,'14.2',false,false);
    $this->AddCampo('valor_anulado' ,'numeric',false,'14.2',false,false);
    $this->AddCampo('recurso'       , 'varchar(200)', false, '', false, false);
    $this->AddCampo('despesa'       , 'varchar(200)', false, '', false, false);

}

function montaRecuperaTodos()
{
    if ($this->getDado("boMostrarAnuladoMesmoPeriodo")) {
        $boMostrarAnuladoMesmoPeriodo = 'true';
    } else {
        $boMostrarAnuladoMesmoPeriodo = 'false';
    }

    $stSql  = "select * \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("exercicio") ."',  \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stDataInicial") . "', \n";
    $stSql .= "  '" . $this->getDado("stDataFinal") . "','".$this->getDado("stEntidade")."',\n";
    $stSql .= "  '" . $this->getDado("inOrgao")."','".$this->getDado("inUnidade")."','".$this->getDado("inCodPao")."',\n";
    $stSql .= "  '" . $this->getDado("inRecurso")."','".str_replace(".","",$this->getDado("stElementoDespesa"))."',\n";
    $stSql .= "  '" . $this->getDado("stDestinacaoRecurso")."','".$this->getDado("inCodDetalhamento")."', \n";
    $stSql .= "  '" . $this->getDado("stElementoDespesa")."','" . $this->getDado("inSituacao")."', \n";
    $stSql .= "  '" . $this->getDado("inCodHistorico")."','" . $this->getDado("stOrdenacao") ."', \n";
    $stSql .= "  '" . $this->getDado("inCodFuncao")."','".$this->getDado("inCodSubFuncao")."','".$this->getDado("inCodPrograma")."', \n";
    $stSql .= "  '" . $this->getDado("inCodPlano")."', '" . $this->getDado("inCodDotacao")."', ".$boMostrarAnuladoMesmoPeriodo." ) as retorno(                        \n";
    $stSql .= "  entidade            integer,                                           \n";
    $stSql .= "  descricao_categoria varchar,                                           \n";
    $stSql .= "  nom_tipo            varchar,                                           \n";
    $stSql .= "  empenho             integer,                                           \n";
    $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  cgm                 integer,                                           \n";
    $stSql .= "  razao_social        varchar,                                           \n";
    $stSql .= "  cod_nota            integer,                                           \n";
    $stSql .= "  data                text,                                              \n";
    $stSql .= "  ordem               integer,                                           \n";
    $stSql .= "  conta               integer,                                           \n";
    $stSql .= "  nome_conta          varchar,                                           \n";
    $stSql .= "  valor               numeric,                                           \n";
    $stSql .= "  valor_anulado       numeric,                                           \n";
    $stSql .= "  descricao           varchar,                                           \n";
    $stSql .= "  recurso             varchar,                                           \n";
    $stSql .= "  despesa             varchar                                            \n";
    $stSql .= "  )                                                                        ";

   return $stSql;
}

function recuperaPagosEstornados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = "";
    $stSql = $this->montaRecuperaPagosEstornados().$stFiltro.$stGroup.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPagosEstornados()
{
    // Implementar aqui

    $stSql  = "select entidade, descricao_categoria, nom_tipo, empenho, exercicio, cgm, Substr(razao_social,1,45) as razao_social, cod_nota, data, ordem, conta, Substr(nome_conta,1,45) as nome_conta, valor, valor_estornado, valor_liquido, descricao, recurso, despesa  \n";
    $stSql .= "  from empenho.fn_empenho_empenhado_pago_estornado('" . $this->getDado("exercicio") ."',                             \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stDataInicial") . "',                                    \n";
    $stSql .= "  '" . $this->getDado("stDataFinal") . "','".$this->getDado("stEntidade")."',                                        \n";
    $stSql .= "  '" . $this->getDado("inOrgao")."','".$this->getDado("inUnidade")."','".$this->getDado("inCodPao")."',              \n";
    $stSql .= "  '" . $this->getDado("inRecurso")."','".str_replace(".","",$this->getDado("stElementoDespesa"))."',                 \n";
    $stSql .= "  '" . $this->getDado("stDestinacaoRecurso")."','".$this->getDado("inCodDetalhamento")."',                           \n";
    $stSql .= "  '" . $this->getDado("stElementoDespesa")."','" . $this->getDado("inSituacao")."',                                  \n";
    $stSql .= "  '" . $this->getDado("inCodHistorico")."','" . $this->getDado("stOrdenacao") ."',                                   \n";
    $stSql .= "  '".$this->getDado("inCodFuncao")."','".$this->getDado("inCodSubFuncao")."','".$this->getDado("inCodPrograma")."',  \n";
    $stSql .= "  '".$this->getDado("inCodPlano")."', '" . $this->getDado("inCodDotacao")."' ) as retorno(                           \n";
    $stSql .= "  entidade            integer,                                                                                       \n";
    $stSql .= "  descricao_categoria varchar,                                                                                       \n";
    $stSql .= "  nom_tipo            varchar,                                                                                       \n";
    $stSql .= "  empenho             integer,                                                                                       \n";
    $stSql .= "  exercicio           char(4),                                                                                       \n";
    $stSql .= "  cgm                 integer,                                                                                       \n";
    $stSql .= "  razao_social        varchar,                                                                                       \n";
    $stSql .= "  cod_nota            integer,                                                                                       \n";
    $stSql .= "  data                text,                                                                                          \n";
    $stSql .= "  ordem               integer,                                                                                       \n";
    $stSql .= "  conta               integer,                                                                                       \n";
    $stSql .= "  nome_conta          varchar,                                                                                       \n";
    $stSql .= "  valor               numeric,                                                                                       \n";
    $stSql .= "  valor_estornado     numeric,                                                                                       \n";
    $stSql .= "  valor_liquido       numeric,                                                                                       \n";
    $stSql .= "  descricao           varchar,                                                                                       \n";
    $stSql .= "  recurso             varchar,                                                                                       \n";
    $stSql .= "  despesa             varchar(150)                                                                                   \n";
    $stSql .= "  )                                                                                                                    ";

    return $stSql;
}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";
    $stSql .= " SELECT SUM(func.vl_original) FROM                        ".$stQuebra;
    $stSql .= " ( ".$this->montaRecuperaTodos()." ) as func              ".$stQuebra;
    $stSql .= " WHERE                                                    ".$stQuebra;
    $stSql .= "     empenho NOT NULL                                 ".$stQuebra;

    return $stSql;
}

}
