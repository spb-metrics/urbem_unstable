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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 18/02/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoRelatorioEmpenhadoPagoLiquidado.class.php 64470 2016-03-01 13:12:50Z jean $

    $Revision: 32880 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRecurso.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoFuncao.class.php"                       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSubfuncao.class.php"                    );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoPrograma.class.php"                     );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoHistorico.class.php"                      );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php"     );

/**
    * Classe de Regra de Negócios Empenho Empenhado, Pago ou Liquidado
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioEmpenhadoPagoLiquidado
{
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var Integer
    * @access Private
*/
var $inOrgao;
/**
    * @var Integer
    * @access Private
*/
var $inUnidade;
/**
    * @var String
    * @access Private
*/
var $stCodElementoDespesa;
/**
    * @var Integer
    * @access Private
*/
var $inRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var String
    * @access Private
*/
var $stFiltro;

/**
    * @var String
    * @access Private
*/
var $stOrdenacao;

/**
    * @var String
    * @access Private
*/
var $stDemonstracaoDescricaoEmpenho;

/**
    * @var String
    * @access Private
*/
var $stDemonstracaoDescricaoRecurso;

/**
    * @var String
    * @access Private
*/
var $stDemonstracaoDescricaoElementoDespesa;

/**
    * @var Integer
    * @access Private
*/
var $inCentroCusto;

/**
     * @access Public
     * @param Object $valor
*/
function setREntidade($valor) { $this->obREntidade     = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodDotacao($valor) { $this->stCodDotacao      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setOrgao($valor) { $this->inOrgao        = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setUnidade($valor) { $this->inUnidade        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodElementoDespesa($valor) { $this->stCodElementoDespesa      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setRecurso($valor) { $this->inRecurso        = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSituacao($valor) { $this->inSituacao        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setOrdenacao($valor) { $this->stOrdenacao            = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setDemonstracaoDescricaoEmpenho($valor) { $this->stDemonstracaoDescricaoEmpenho     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDemonstracaoDescricaoRecurso($valor) { $this->stDemonstracaoDescricaoRecurso = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDemonstracaoDescricaoElementoDespesa($valor) { $this->stDemonstracaoDescricaoElementoDespesa = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCentroCusto($valor) { $this->inCentroCusto = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getREntidade() { return $this->obREntidade;               }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return String
*/
function getCodDotacao() { return $this->stCodDotacao;                }
/**
     * @access Public
     * @return String
*/
function getDataInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return String
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Integer
*/
function getOrgao() { return $this->inOrgao;                  }
/**
     * @access Public
     * @return Integer
*/
function getUnidade() { return $this->inUnidade;                  }
/**
     * @access Public
     * @return String
*/
function getCodElementoDespesa() { return $this->stCodElementoDespesa;                }
/**
     * @access Public
     * @return Integer
*/
function getRecurso() { return $this->inRecurso;                  }
/**
     * @access Public
     * @return Integer
*/
function getSituacao() { return $this->inSituacao;                  }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro;                     }

/**
     * @access Public
     * @return String
*/
function getOrdenacao() { return $this->stOrdenacao;                     }

/**
     * @access Public
     * @return String
*/
function getDemonstracaoDescricaoEmpenho() { return $this->stDemonstracaoDescricaoEmpenho;                     }

/**
     * @access Public
     * @return String
*/
function getDemonstracaoDescricaoRecurso() { return $this->stDemonstracaoDescricaoRecurso; }

/**
     * @access Public
     * @return String
*/
function getDemonstracaoDescricaoElementoDespesa() { return $this->stDemonstracaoDescricaoElementoDespesa; }

/**
     * @access Public
     * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio; }

/**
     * @access Public
     * @return Integer
*/
function getCentroCusto() { return $this->inCentroCusto; }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioEmpenhadoPagoLiquidado()
{
    $this->setREntidade                     ( new ROrcamentoEntidade                );
    $this->obREntidade->obRCGM->setNumCGM   ( Sessao::read('numCgm')                       );
    $this->obROrcamentoUnidadeOrcamentaria       = new ROrcamentoUnidadeOrcamentaria;
    $this->obROrcamentoClassificacaoDespesa      = new ROrcamentoClassificacaoDespesa;
    $this->obROrcamentoOrgaoOrcamentario         = new ROrcamentoOrgaoOrcamentario;
    $this->obROrcamentoRecurso                   = new ROrcamentoRecurso;
    $this->obROrcamentoProjetoAtividade          = new ROrcamentoProjetoAtividade;
    $this->obROrcamentoFuncao                    = new ROrcamentoFuncao;
    $this->obROrcamentoSubfuncao                 = new ROrcamentoSubfuncao;
    $this->obROrcamentoPrograma                  = new ROrcamentoPrograma;
    $this->obREmpenhoHistorico                   = new REmpenhoHistorico;
    $this->obRContabilidadePlanoContaAnalitica   = new RContabilidadePlanoContaAnalitica;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhadoPagoLiquidado.class.php" );
    $obFEmpenhoEmpenhadoPagoLiquidado = new FEmpenhoEmpenhadoPagoLiquidado;

    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("exercicio",$this->getExercicio());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stFiltro",$this->getFiltro());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stEntidade",$stEntidade);
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stDataInicial",$this->getDataInicial());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stDataFinal",$this->getDataFinal());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inOrgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inUnidade",$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodFuncao"    , $this->obROrcamentoFuncao->getCodigoFuncao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodSubFuncao" , $this->obROrcamentoSubfuncao->getCodigoSubFuncao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodPrograma"  , $this->obROrcamentoPrograma->getCodPrograma());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodPao",$this->obROrcamentoProjetoAtividade->getNumeroProjeto());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stElementoDespesa",$this->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodHistorico",$this->obREmpenhoHistorico->getCodHistorico());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inRecurso",$this->obROrcamentoRecurso->getCodRecurso());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stDestinacaoRecurso",$this->obROrcamentoRecurso->getDestinacaoRecurso());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodDetalhamento",  $this->obROrcamentoRecurso->getCodDetalhamento() );
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inSituacao",$this->getSituacao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodPlano",$this->obRContabilidadePlanoContaAnalitica->getCodPlano());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stOrdenacao",$this->getOrdenacao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("stDemonstracaoDescricaoEmpenho",$this->getDemonstracaoDescricaoEmpenho());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCodDotacao",$this->getCodDotacao());
    $obFEmpenhoEmpenhadoPagoLiquidado->setDado("boMostrarAnuladoMesmoPeriodo", true);

    if (Sessao::getExercicio() > '2015') {
        $obFEmpenhoEmpenhadoPagoLiquidado->setDado("inCentroCusto", $this->getCentroCusto());
    }

    $obErro = $obFEmpenhoEmpenhadoPagoLiquidado->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $inAnulado          = 0;
    $inTotalAnulado     = 0;
    $inSaldo            = 0;
    $inTotalSaldo       = 0;
    $arRecord           = array();
    $dtAtual            = "";
    $razao_social_atual = "";
    $mostra             = true;

    while ( !$rsRecordSet->eof() ) {
        if ($this->getOrdenacao() == 'credor_data') {
            $razao_social = trim($rsRecordSet->getCampo('razao_social'));

            if (($razao_social_atual <> $razao_social) and $inCount>0) {
                $razao_social_atual = $razao_social;

                //MONTA TOTALIZADOR GERAL
                $arRecord[$inCount]['nivel']             = 2;
                $arRecord[$inCount]['empenho']           = "";
                $arRecord[$inCount]['cod_nota']          = "";
                $arRecord[$inCount]['cgm']               = "";
                $arRecord[$inCount]['razao_social']      = "TOTAL DO CREDOR";
                $arRecord[$inCount]['descricao_categoria'] = "";
                $arRecord[$inCount]['nom_tipo']          = "";
                $arRecord[$inCount]['valor']             = number_format( $inTotal, 2, ',', '.' );
                $arRecord[$inCount]['valor_anulado']     = number_format( $inAnulado, 2, ',', '.' );
                $arRecord[$inCount]['saldo']             = number_format( $inSaldo, 2, ',', '.' );
                $arRecord[$inCount]['data']              = "";
                $arRecord[$inCount]['ordem']             = "";
                $arRecord[$inCount]['conta']             = "";
                $arRecord[$inCount]['nome_conta']        = "";

                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['empenho']           = "";
                $arRecord[$inCount]['cod_nota']          = "";
                $arRecord[$inCount]['cgm']               = "";
                $arRecord[$inCount]['razao_social']      = "";
                $arRecord[$inCount]['descricao_categoria'] = "";
                $arRecord[$inCount]['nom_tipo']          = "";
                $arRecord[$inCount]['valor']             = "";
                $arRecord[$inCount]['valor_anulado']     = "";
                $arRecord[$inCount]['saldo']             = "";
                $arRecord[$inCount]['data']              = "";
                $arRecord[$inCount]['ordem']             = "";
                $arRecord[$inCount]['conta']             = "";
                $arRecord[$inCount]['nome_conta']        = "";

                $inCount++;

                $inTotalGeral += $inTotal;
                $inTotalAnulado += $inAnulado;
                $inTotalSaldo += $inSaldo;
                $inSaldo = 0;
                $inTotal = 0;
                $inAnulado = 0;
                $mostra = true;
            }

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
            if($rsRecordSet->getCampo('cod_nota') <> 0)
                $arRecord[$inCount]['cod_nota']      = $rsRecordSet->getCampo('cod_nota');
            else
                $arRecord[$inCount]['cod_nota']      = "";
            $arRecord[$inCount]['data']              = $rsRecordSet->getCampo('data');
            $arRecord[$inCount]['valor']             = number_format( $rsRecordSet->getCampo('valor'), 2, ',', '.' );
            $arRecord[$inCount]['valor_anulado']     = number_format( $rsRecordSet->getCampo('valor_anulado'), 2, ',', '.' );
            $arRecord[$inCount]['saldo']             = number_format( ($rsRecordSet->getCampo('valor') - $rsRecordSet->getCampo('valor_anulado')), 2, ',', '.' );
            if ($mostra) {
                $arRecord[$inCount]['razao_social']  = $razao_social;
                $arRecord[$inCount]['cgm']           = $rsRecordSet->getCampo('cgm');
            } else {
                $arRecord[$inCount]['razao_social']  = "";
                $arRecord[$inCount]['cgm']  = "";
            }
            $arRecord[$inCount]['descricao_categoria']       = $rsRecordSet->getCampo('descricao_categoria');
            $arRecord[$inCount]['nom_tipo']    = $rsRecordSet->getCampo('nom_tipo');
            $arRecord[$inCount]['ordem']       = $rsRecordSet->getCampo('ordem');
            $arRecord[$inCount]['conta']       = $rsRecordSet->getCampo('conta');
            $arRecord[$inCount]['nome_conta']  = $rsRecordSet->getCampo('nome_conta');
            $arRecord[$inCount]['recurso']     = $rsRecordSet->getCampo('recurso');
            $arRecord[$inCount]['despesa']     = $rsRecordSet->getCampo('despesa');

            if($inCount == 0)
                $razao_social_atual = $razao_social;

            $inCount++;
            $inTotal   += $rsRecordSet->getCampo('valor');
            $inAnulado += $rsRecordSet->getCampo('valor_anulado');
            $inSaldo   += ( $rsRecordSet->getCampo('valor') - $rsRecordSet->getCampo('valor_anulado') );

            $mostra = false;
            if ($this->getDemonstracaoDescricaoEmpenho() == "S" && $rsRecordSet->getCampo('descricao') != null) {
                $stNomContaTemp = str_replace( chr(10), "", trim($rsRecordSet->getCampo('descricao')) );
                $stNomContaTemp = str_replace( chr(13).chr(10), " ", trim($rsRecordSet->getCampo('descricao')) );
                $stNomContaTemp = wordwrap( $stNomContaTemp,100,chr(13) );
                $arNomContaOLD = explode( chr(13), $stNomContaTemp );
                //fim de alterações para quebra de linha
                $inCount2 = $inCount;
                //alterações para quebra de linha
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord[$inCount2]['empenho'] = $stNomContaTemp;
                    $inCount2++;
                }
                $inCount = $inCount2;
            }

        } else {
            $data = $rsRecordSet->getCampo('data');

            if (($dtAtual <> $data) and $inCount>0) {
                $dtAtual = $data;

                //MONTA TOTALIZADOR GERAL
                $arRecord[$inCount]['nivel']             = 2;
                $arRecord[$inCount]['empenho']           = "";
                $arRecord[$inCount]['cod_nota']          = "";
                $arRecord[$inCount]['cgm']               = "";
                $arRecord[$inCount]['razao_social']      = "TOTAL DO DIA";
                $arRecord[$inCount]['descricao_categoria'] = "";
                $arRecord[$inCount]['nom_tipo']          = "";
                $arRecord[$inCount]['valor']             = number_format( $inTotal, 2, ',', '.' );
                $arRecord[$inCount]['valor_anulado']     = number_format( $inAnulado, 2, ',', '.' );
                $arRecord[$inCount]['saldo']             = number_format( $inSaldo, 2, ',', '.' );
                $arRecord[$inCount]['data']              = "";
                $arRecord[$inCount]['ordem']             = "";
                $arRecord[$inCount]['conta']             = "";
                $arRecord[$inCount]['nome_conta']        = "";

                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['empenho']           = "";
                $arRecord[$inCount]['cod_nota']          = "";
                $arRecord[$inCount]['cgm']               = "";
                $arRecord[$inCount]['razao_social']      = "";
                $arRecord[$inCount]['descricao_categoria'] = "";
                $arRecord[$inCount]['nom_tipo']          = "";
                $arRecord[$inCount]['valor']             = "";
                $arRecord[$inCount]['valor_anulado']     = "";
                $arRecord[$inCount]['saldo']             = "";
                $arRecord[$inCount]['data']              = "";
                $arRecord[$inCount]['ordem']             = "";
                $arRecord[$inCount]['conta']             = "";
                $arRecord[$inCount]['nome_conta']        = "";

                $inCount++;

                $inTotalGeral += $inTotal;
                $inTotalAnulado += $inAnulado;
                $inTotalSaldo += $inSaldo;
                $inTotal = 0;
                $inAnulado = 0;
                $inSaldo = 0;
                $mostra = true;
            }

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
            if($rsRecordSet->getCampo('cod_nota') <> 0)
                $arRecord[$inCount]['cod_nota']      = $rsRecordSet->getCampo('cod_nota');
            else
                $arRecord[$inCount]['cod_nota']      = "";
            $arRecord[$inCount]['cgm']               = $rsRecordSet->getCampo('cgm');
            $arRecord[$inCount]['razao_social']      = $rsRecordSet->getCampo('razao_social');
            $arRecord[$inCount]['valor']             = number_format( $rsRecordSet->getCampo('valor'), 2, ',', '.' );
            $arRecord[$inCount]['valor_anulado']     = number_format( $rsRecordSet->getCampo('valor_anulado'), 2, ',', '.' );
            $arRecord[$inCount]['saldo']             = number_format(  ($rsRecordSet->getCampo('valor') - $rsRecordSet->getCampo('valor_anulado')), 2, ',', '.' );

            if($mostra)
                $arRecord[$inCount]['data']              = $data;
            else
                $arRecord[$inCount]['data']              = "";

            $arRecord[$inCount]['descricao_categoria']       = $rsRecordSet->getCampo('descricao_categoria');
            $arRecord[$inCount]['nom_tipo']    = $rsRecordSet->getCampo('nom_tipo');
            $arRecord[$inCount]['ordem']       = $rsRecordSet->getCampo('ordem');
            $arRecord[$inCount]['conta']       = $rsRecordSet->getCampo('conta');
            $arRecord[$inCount]['nome_conta']  = $rsRecordSet->getCampo('nome_conta');
            $arRecord[$inCount]['recurso']     = $rsRecordSet->getCampo('recurso');
            $arRecord[$inCount]['despesa']     = $rsRecordSet->getCampo('despesa');

            if($inCount == 0)
                $dtAtual = $data;

            $inCount++;
            $inTotal += $rsRecordSet->getCampo('valor');
            $inAnulado += $rsRecordSet->getCampo('valor_anulado');
            $inSaldo += ( $rsRecordSet->getCampo('valor') - $rsRecordSet->getCampo('valor_anulado'));

            $mostra = false;
            if ($this->getDemonstracaoDescricaoEmpenho() == "S" && $rsRecordSet->getCampo('descricao') != null) {
                $stNomContaTemp = str_replace( chr(10), "", trim($rsRecordSet->getCampo('descricao')) );
                $stNomContaTemp = str_replace( chr(13).chr(10), " ", trim($rsRecordSet->getCampo('descricao')) );
                $stNomContaTemp = wordwrap( $stNomContaTemp,100,chr(13) );
                $arNomContaOLD = explode( chr(13), $stNomContaTemp );
                //fim de alterações para quebra de linha
                $inCount2 = $inCount;
                //alterações para quebra de linha
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arRecord[$inCount2]['empenho'] = $stNomContaTemp;
                    $inCount2++;
                }
                $inCount = $inCount2;
            }
        }
        $rsRecordSet->proximo();

    }

    if ($inCount>0) {

        $stTitulo = 'TOTAL DO PERÍODO';
        if ( ($this->getTipoRelatorio() == 'empenhado') || ($this->getTipoRelatorio() == 'liquidado') ) {
            $stTitulo = 'SUB-TOTAL DO PERÍODO';
        }

        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = "TOTAL DO DIA";
        $arRecord[$inCount]['valor']             = number_format( $inTotal, 2, ',', '.' );
        $arRecord[$inCount]['valor_anulado']     = number_format( $inAnulado, 2, ',', '.' );
        $arRecord[$inCount]['saldo']             = number_format( $inSaldo, 2, ',', '.' );
        $arRecord[$inCount]['data']              = "";
        $arRecord[$inCount]['ordem']              = "";
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['nome_conta']        = "";
        $inTotalGeral += $inTotal;
        $inTotalSaldo += $inSaldo;
        $inTotalAnulado += $inAnulado;

        $inCount++;

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = "";
        $arRecord[$inCount]['valor']             = "";
        $arRecord[$inCount]['valor_anulado']     = "";
        $arRecord[$inCount]['saldo']             = "";
        $arRecord[$inCount]['data']              = "";
        $arRecord[$inCount]['ordem']             = "";
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['nome_conta']        = "";

        $inCount++;

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['empenho']           = "";
        $arRecord[$inCount]['cod_nota']          = "";
        $arRecord[$inCount]['cgm']               = "";
        $arRecord[$inCount]['razao_social']      = $stTitulo;
        $arRecord[$inCount]['valor']             = number_format( $inTotalGeral, 2, ',', '.' );
        $arRecord[$inCount]['valor_anulado']     = number_format( $inTotalAnulado, 2, ',', '.' );
        $arRecord[$inCount]['saldo']             = number_format( $inTotalSaldo, 2, ',', '.' );
        $arRecord[$inCount]['data']              = "";
        $arRecord[$inCount]['ordem']             = "";
        $arRecord[$inCount]['conta']             = "";
        $arRecord[$inCount]['nome_conta']        = "";
        $inCount++;
    }

    // Se o relatorio for da situacao empenhado, deve listar
    if ( ($this->getTipoRelatorio() == 'empenhado') || ($this->getTipoRelatorio() == 'liquidado') ) {

        if ($inCount > 0) {
            // Espaço em branco
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['cgm']               = "";
            $arRecord[$inCount]['descricao_categoria'] = "";
            $arRecord[$inCount]['nom_tipo']          = "";
            $arRecord[$inCount]['razao_social']      = "";
            $arRecord[$inCount]['valor']             = "";
            $arRecord[$inCount]['valor_anulado']     = "";
            $arRecord[$inCount]['saldo']             = "";
            $arRecord[$inCount]['data']              = "";
            $arRecord[$inCount]['ordem']             = "";
            $arRecord[$inCount]['conta']             = "";
            $arRecord[$inCount]['nome_conta']        = "";
            $inCount++;
        }

        $obFEmpenhoEmpenhadoPagoLiquidado->setDado("boMostrarAnuladoMesmoPeriodo", false);
        $obErro = $obFEmpenhoEmpenhadoPagoLiquidado->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
        
        $dtAnulacao = "";
        $dtAnulacaoAtual = "";
        $mostra = false;
        $flValor = 0.00;
        while ( !$rsRecordSet->eof() ) {

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
            if($rsRecordSet->getCampo('cod_nota') <> 0)
                $arRecord[$inCount]['cod_nota']      = $rsRecordSet->getCampo('cod_nota');
            else
                $arRecord[$inCount]['cod_nota']      = "";
            $arRecord[$inCount]['cgm']               = $rsRecordSet->getCampo('cgm');
            $arRecord[$inCount]['razao_social']      = $rsRecordSet->getCampo('razao_social');
            $arRecord[$inCount]['valor']             = number_format( $flValor, 2, ',', '.' );
            $arRecord[$inCount]['valor_anulado']     = number_format( $rsRecordSet->getCampo('valor_anulado'), 2, ',', '.' );
            $arRecord[$inCount]['saldo']             = number_format(  ($flValor - $rsRecordSet->getCampo('valor_anulado')), 2, ',', '.' );

            $flValorOutroPeriodo += $flValor;
            $flValorAnuladoOutroPeriodo += $rsRecordSet->getCampo('valor_anulado');
            $flSaldoOutroPeriodo += ($flValor - $rsRecordSet->getCampo('valor_anulado'));

            $arRecord[$inCount]['data']              = $rsRecordSet->getCampo('data');
            $arRecord[$inCount]['descricao_categoria']       = $rsRecordSet->getCampo('descricao_categoria');
            $arRecord[$inCount]['nom_tipo']    = $rsRecordSet->getCampo('nom_tipo');
            $arRecord[$inCount]['ordem']       = $rsRecordSet->getCampo('ordem');
            $arRecord[$inCount]['conta']       = $rsRecordSet->getCampo('conta');
            $arRecord[$inCount]['nome_conta']  = $rsRecordSet->getCampo('nome_conta');
            $arRecord[$inCount]['recurso']     = $rsRecordSet->getCampo('recurso');
            $arRecord[$inCount]['despesa']     = $rsRecordSet->getCampo('despesa');

            $inCount++;
            $mostra = false;
            $rsRecordSet->proximo();
        }     
                
        if ($inCount > 0) {

            //MONTA TOTALIZADOR GERAL
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['cgm']               = "";
            $arRecord[$inCount]['descricao_categoria'] = "";
            $arRecord[$inCount]['nom_tipo']          = "";
            $arRecord[$inCount]['razao_social']      = '';
            $arRecord[$inCount]['valor']             = '';
            $arRecord[$inCount]['valor_anulado']     = '';
            $arRecord[$inCount]['saldo']             = '';
            $arRecord[$inCount]['data']              = "";
            $arRecord[$inCount]['ordem']             = "";
            $arRecord[$inCount]['conta']             = "";
            $arRecord[$inCount]['nome_conta']        = "";

            $inCount++;
            //MONTA TOTALIZADOR GERAL
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['empenho']           = "";
            $arRecord[$inCount]['cod_nota']          = "";
            $arRecord[$inCount]['cgm']               = "";
            $arRecord[$inCount]['descricao_categoria'] = "";
            $arRecord[$inCount]['nom_tipo']          = "";
            $arRecord[$inCount]['razao_social']      = 'TOTAL DO PERÍODO';
            
            if($this->getSituacao() != "3"){
                $arRecord[$inCount]['valor']         = number_format( ($inTotalGeral + $flValorOutroPeriodo), 2, ',', '.' );
                $arRecord[$inCount]['valor_anulado'] = number_format( ($inTotalAnulado + $flValorAnuladoOutroPeriodo), 2, ',', '.' );
                $arRecord[$inCount]['saldo']         = number_format( ($inTotalSaldo + $flSaldoOutroPeriodo), 2, ',', '.' );
            }else{
                $obFEmpenhoEmpenhadoPagoLiquidado->recuperaLiquidadoTotal( $rsLiquidadoTotal );
                $obFEmpenhoEmpenhadoPagoLiquidado->recuperaLiquidadoAnuladoTotal( $rsLiquidadoTotalAnulado );
                
                $arRecord[$inCount]['valor']         = number_format( $rsLiquidadoTotal->getCampo('vl_total'), 2, ',', '.' );
                $arRecord[$inCount]['valor_anulado'] = number_format( ($inTotalAnulado + $flValorAnuladoOutroPeriodo), 2, ',', '.' );
                $arRecord[$inCount]['saldo']         = number_format( $rsLiquidadoTotal->getCampo('vl_total') - $rsLiquidadoTotalAnulado->getCampo('vl_total_anulado'), 2, ',', '.' );
            }
            
            $arRecord[$inCount]['data']              = "";
            $arRecord[$inCount]['ordem']             = "";
            $arRecord[$inCount]['conta']             = "";
            $arRecord[$inCount]['nome_conta']        = "";

            $inCount++;
        }

    }

    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['empenho']           = " ";
    $arRecord[$inCount]['cod_nota']          = "";
    $arRecord[$inCount]['cgm']               = " ";
    $arRecord[$inCount]['descricao_categoria'] = "";
    $arRecord[$inCount]['nom_tipo']          = "";
    $arRecord[$inCount]['razao_social']      = "- ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['valor']             = " ";
    $arRecord[$inCount]['valor_anulado']     = " ";
    $arRecord[$inCount]['saldo']             = " ";
    $arRecord[$inCount]['data']              = " ";
    $arRecord[$inCount]['ordem']             = "";
    $arRecord[$inCount]['conta']             = "";
    $arRecord[$inCount]['nome_conta']        = "";
    $arRecord[$inCount]['recurso']           = "";
    $arRecord[$inCount]['despesa']           = "";

    $this->obREntidade->setExercicio( $this->getExercicio() );
    $inEntidades = str_replace("'","",$this->getCodEntidade() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['razao_social'] = $rsLista->getCampo("entidade");
    }

    if ($this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()) {
        $this->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
        $this->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- ORGÃO";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsOrgao->getCampo("nom_orgao");
    }
    if ($this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade()) {
        $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
        $this->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
        $this->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- UNIDADE";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsCombo->getCampo("nom_unidade");
    }
    if ($this->obROrcamentoClassificacaoDespesa->getCodEstrutural()) {
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- ELEMENTO DE DESPESA";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $this->obROrcamentoClassificacaoDespesa->getCodEstrutural();
    }
    if ($this->obROrcamentoRecurso->getCodRecurso()) {
        $this->obROrcamentoRecurso->setExercicio(Sessao::getExercicio());
        $this->obROrcamentoRecurso->listar( $rsRecurso );

        $inCount++;
        $arRecord[$inCount]['razao_social'] = "";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = "- RECURSO";
        $inCount++;
        $arRecord[$inCount]['razao_social'] = $rsRecurso->getCampo("nom_recurso");
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
