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
    * Classe de Regra do Relatório de Modelos Executivo 5
    * Data de Criação   : 20/05/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso :uc-02.05.07
*/

/*
$Log$
Revision 1.6  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFModelosExecutivo.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Modelos Executivo
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RLRFRelatorioModelos5 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFModelosExecutivo;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @var Object
    * @access Private
*/
var $inCodModelo;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
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
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stTipoValorDespesa;

/**
    * @var Integer
    * @access Private
*/
function setFLRFModelosExecutivo($valor) { $this->obFLRFModelosExecutivo  = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal               = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTipoValorDespesa($valor) { $this->stTipoValorDespesa           = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFLRFModelosExecutivo() { return $this->obFLRFModelosExecutivo;   }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade  ;        }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModelo() { return $this->inCodModelo;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;                 }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                   }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicial() { return $this->stDataInicial;            }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinal() { return $this->stDataFinal;              }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                      }
/**
     * @access Public
     * @return Object
*/
function getTipoValorDespesa() { return $this->stTipoValorDespesa;           }

/**
     * @access Public
     * @return Object
*/
function RLRFRelatorioModelos5()
{
    $sessao = $_SESSION ['sessao'];
    $this->setFLRFModelosExecutivo       ( new FLRFModelosExecutivo         );
    $this->obROrcamentoEntidade          = new ROrcamentoEntidade;
    $this->obRRelatorio                  = new RRelatorio;
    $this->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet ,&$rsRecordSet1, &$rsRecordSetTotal, $stOrder = "")
{
    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $this->obFLRFModelosExecutivo->setDado("inCodModelo",$this->getCodModelo());
    $this->obFLRFModelosExecutivo->setDado("stDataInicial",$this->getDataInicial());
    $this->obFLRFModelosExecutivo->setDado("stDataFinal",$this->getDataFinal());
    $this->obFLRFModelosExecutivo->setDado("exercicio",$this->getExercicio());
    $this->obFLRFModelosExecutivo->setDado("stEntidade",$this->getCodEntidade());
    $this->obFLRFModelosExecutivo->setDado("stFiltro",$this->getFiltro());
    $this->obFLRFModelosExecutivo->setDado("stTipoValorDespesa",$this->getTipoValorDespesa());
    $obErro = $this->obFLRFModelosExecutivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
//    $this->obFLRFModelosExecutivo->debug();

    $inCount            = 0;
    $inCount2           = 0;
    $inTotal            = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $arRecord1          = array();
    $arRecordTotal      = array();

    while ( !$rsRecordSet->eof()) {
        if ($inCount < 6) {
           if ($inCount == 0) {
              $arRecord[$inCount]['coluna1'] = 'EXTERNAS';
              $arRecord[$inCount]['coluna2'] = '';
              $arRecord[$inCount]['coluna3'] = '';
              $arRecord[$inCount]['coluna4'] = '';
              $arRecord[$inCount]['coluna5'] = '';
              $inCount++;
           }
           if ($inCount == 3) {
              $arRecord[$inCount]['coluna1'] = 'INTERNAS';
              $arRecord[$inCount]['coluna2'] = '';
              $arRecord[$inCount]['coluna3'] = '';
              $arRecord[$inCount]['coluna4'] = '';
              $arRecord[$inCount]['coluna5'] = '';
              $inCount++;
           }

           $arRecord[$inCount]['coluna1']    = "   ".$rsRecordSet->getCampo('nom_conta');
           $arRecord[$inCount]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
           $flVlContabil                     = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
           $arRecord[$inCount]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
           $flVlAjuste                       = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
           $arRecord[$inCount]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
           $flVlAjustado                     = bcadd($rsRecordSet->getCampo('vl_ajustado'),$flVlAjustado,4);
           $arRecord[$inCount]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

           $arRecordTotal[0]['coluna1']    = 'TOTAL DAS GARANTIAS DE VALORES';
           $arRecordTotal[0]['coluna2']    =  number_format($flVlContabil, 2, ',', '.' );
           $arRecordTotal[0]['coluna3']    =  number_format($flVlAjuste, 2, ',', '.' );
           $arRecordTotal[0]['coluna4']    =  number_format($flVlAjustado, 2, ',', '.' );
           $inCount++;

        } else {
           if ($inCount2 == 0) {
              $flVlContabil = 0;
              $flVlAjustado = 0;
              $flVlAjuste   = 0;
              $arRecord1[$inCount2]['coluna1'] = 'EXTERNAS';
              $arRecord1[$inCount2]['coluna2'] = '';
              $arRecord1[$inCount2]['coluna3'] = '';
              $arRecord1[$inCount2]['coluna4'] = '';
              $arRecord1[$inCount2]['coluna5'] = '';
              $inCount2++;
           }
           if ($inCount2 == 3) {
              $arRecord1[$inCount2]['coluna1'] = 'INTERNAS';
              $arRecord1[$inCount2]['coluna2'] = '';
              $arRecord1[$inCount2]['coluna3'] = '';
              $arRecord1[$inCount2]['coluna4'] = '';
              $arRecord1[$inCount2]['coluna5'] = '';
              $inCount2++;
           }

           $arRecord1[$inCount2]['coluna1']    = "   ".$rsRecordSet->getCampo('nom_conta');
           $arRecord1[$inCount2]['coluna2']    = $rsRecordSet->getCampo('cod_estrutural');
           $flVlContabil                       = bcadd($flVlContabil,$rsRecordSet->getCampo('vl_contabil'),4);
           $arRecord1[$inCount2]['coluna3']    = number_format($rsRecordSet->getCampo('vl_contabil'), 2, ',', '.' );
           $flVlAjuste                         = bcadd($flVlAjuste,$rsRecordSet->getCampo('vl_ajuste'),4);
           $arRecord1[$inCount2]['coluna4']    = number_format($rsRecordSet->getCampo('vl_ajuste'), 2, ',', '.' );
           $flVlAjustado                       = bcadd($rsRecordSet->getCampo('vl_ajustado'),$flVlAjustado,4);
           $arRecord1[$inCount2]['coluna5']    = number_format($rsRecordSet->getCampo('vl_ajustado'), 2, ',', '.' );

           $arRecordTotal[1]['coluna1']    = 'TOTAL DAS CONTRAGARANTIAS DE VALORES';
           $arRecordTotal[1]['coluna2']    =  number_format($flVlContabil, 2, ',', '.' );
           $arRecordTotal[1]['coluna3']    =  number_format($flVlAjuste, 2, ',', '.' );
           $arRecordTotal[1]['coluna4']    =  number_format($flVlAjustado, 2, ',', '.' );
           $inCount2++;

         }
         $rsRecordSet->proximo();

    }

    $rsRecordSet      = new RecordSet;
    $rsRecordSet1     = new RecordSet;
    $rsRecordSetTotal = new RecordSet;

    $rsRecordSet->preenche( $arRecord );
    $rsRecordSet1->preenche( $arRecord1 );
    $rsRecordSetTotal->preenche( $arRecordTotal );

    return $obErro;
}

}
