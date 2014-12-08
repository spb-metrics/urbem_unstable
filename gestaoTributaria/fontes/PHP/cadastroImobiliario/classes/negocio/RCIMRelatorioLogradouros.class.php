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
 * Classe de regra de Relatório de Logradouro
 * Data de Criação: 23/03/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo B. Paulino

 * @package URBEM
 * @subpackage Regra

 * $Id: RCIMRelatorioLogradouros.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

/**
    * Classe de Regra para relatório de Logradouros
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioLogradouros extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTLogradouro;
/**
    * @var Object
    * @access Private
*/
var $obRCIMLogradouro;

/**
    * @access Public
    * @param String $valor
*/
function setCodInicio($valor) { $this->inCodInicio         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodInicioBairro($valor) { $this->inCodInicioBairro   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodInicioCEP($valor) { $this->inCodInicioCEP      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTermino($valor) { $this->inCodTermino        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTerminoBairro($valor) { $this->inCodTerminoBairro = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setCodTerminoCEP($valor) { $this->inCodTerminoCEP    = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder            = $valor;  }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;       }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioBairro() { return $this->inCodInicioBairro; }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioCEP() { return $this->inCodInicioCEP;     }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;       }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoBairro() { return $this->inCodTerminoBairro; }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoCEP() { return $this->inCodTerminoCEP;    }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;            }

/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioLogradouros()
{
    $this->obTLogradouro    = new TLogradouro;
    $this->obRCIMLogradouro = new RCIMLogradouro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->obRCIMLogradouro->getCodigoMunicipio() ) {
        $stFiltro .= " cod_municipio  = ".$this->obRCIMLogradouro->getCodigoMunicipio()." \r\n AND";
    }// filtro codigo da uf
    if ( $this->obRCIMLogradouro->getCodigoUF() ) {
        $stFiltro .= " cod_uf = ".$this->obRCIMLogradouro->getCodigoUF()." \r\n AND";
    }// filtra tipo logradouro
    if ( $this->obRCIMLogradouro->getCodigoTipo() ) {
        $stFiltro .= " cod_tipo  = ".$this->obRCIMLogradouro->getCodigoTipo()." \r\n AND";
    }//filtra nome do bairro
    if ( $this->obRCIMLogradouro->obRCIMBairro->getNomeBairro() ) {
        $stFiltro .= " UPPER( nom_bairro ) like UPPER( '".$this->obRCIMLogradouro->obRCIMBairro->getNomeBairro()."%' )\r\n AND";
    }//filtra nome logradouro
    if ( $this->obRCIMLogradouro->getNomeLogradouro() ) {
        $stFiltro .= " UPPER( nom_logradouro ) like UPPER( '".$this->obRCIMLogradouro->getNomeLogradouro()."%' )\r\n AND";
    }
// filtros com between
    // INICIO E TERMINO PARA CODIGO DO LOGRADOURO
    if ( $this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " cod_logradouro BETWEEN  ".$this->getCodInicio()."";
        $stFiltro .= " AND ".$this->getCodTermino()." \r\n AND";
    }
    if ( $this->getCodInicio() && !$this->getCodTermino() ) {
        $stFiltro .= " cod_logradouro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND (select max(cod_logradouro) from ".$this->obRCIMLogradouro->obTLogradouro->getTabela().")  \r\n AND";
    }
    if ( !$this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " cod_logradouro BETWEEN  0";
        $stFiltro .= " AND ".$this->getCodTermino()." \r\n AND";
   }
// cod bairro
    if ( $this->getCodInicioBairro() && $this->getCodTerminoBairro() ) {
        $stFiltro .= " cod_bairro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND ".$this->getCodTerminoBairro()." \r\n AND";
    }
    if ( $this->getCodInicioBairro() && !$this->getCodTerminoBairro() ) {
        $stFiltro .= " cod_bairro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND (select max(cod_bairro) from ".$this->obRCIMLogradouro->obRCIMBairro->obTBairro->getTabela().")  \r\n AND";
    }
    if ( !$this->getCodInicioBairro() && $this->getCodTerminoBairro() ) {
        $stFiltro .= " cod_bairro BETWEEN  0";
        $stFiltro .= " AND ".$this->getCodTerminoBairro()." \r\n AND";
   }
// cod cep
    if ( $this->getCodInicioCEP() && $this->getCodTerminoCEP() ) {
        $stFiltro .= " cep BETWEEN  '".$this->getCodInicioCEP()."'";
        $stFiltro .= " AND '".$this->getCodTerminoCEP()."' \r\nAND";
    }
    if ( $this->getCodInicioCEP() && !$this->getCodTerminoCEP() ) {
        $stFiltro .= " cep BETWEEN  '".$this->getCodInicioCEP()."'";
        $stFiltro .= " AND '99999999' AND\r\n";
    }
    if ( !$this->getCodInicioCEP() && $this->getCodTerminoCEP() ) {
        $stFiltro .= " cep BETWEEN  '0'";
        $stFiltro .= " AND '".$this->getCodTerminoCEP()."' \r\nAND";
   }

    if ($stFiltro) {
        $stFiltro = "\r\n WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
// Seleciona ordenação
    if ($this->stOrder == "codlogradouro") {
        $stOrder = " ORDER BY cod_logradouro , cod_uf , cod_municipio ";
    } else {
        $stOrder = " ORDER BY nom_logradouro, cod_uf , cod_municipio  ";
    }

    $obErro = $this->obTLogradouro->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    $arRecord    = array();
    $arCEP       = array();
    $inCount     = 0;
    $inFirstLoop = true;
    $countCEPAnterior = $inCountLoop = 0;

    while ( !$rsRecordSet->eof() ) {
        if ( $inFirstLoop == true OR ( $inCodLogradouroAnterior != $rsRecordSet->getCampo('cod_logradouro') ) ) {
            if ($countCEPAnterior >= $inCountLoop AND $inFirstLoop == false) {
                $z = $countCEPAnterior - 1;
                $y = $inCountLoop;
                $w = $inCount;
                for ($i = $z; $i >= $y; $i--) {
                    $arRecord[$w]['pagina'] = 0;
                    $arRecord[$w]['sigla_uf'] = "";
                    $arRecord[$w]['nom_municipio'] = "";
                    $arRecord[$w]['nom_tipo'] = "";
                    $arRecord[$w]['nom_logradouro'] = "";
                    $arRecord[$w]['cod_logradouro'] = "";
                    $arRecord[$w]['bairros'] = "";
                    $arRecord[$w]['cep'] = trim($arCEP[$i]);
                    $w++;
                }
                $inCount = $w;
            }
            $arCEP    = explode( "," , $rsRecordSet->getCampo('cep') );
            $countCEP = count($arCEP);
            if ($countCEP == 1 AND $arCEP[0] == "") {
                $countCEP = 0;
            }
            $inCountLoop = 0;
        }
        if( $inFirstLoop == true OR
            (
                $rsRecordSet->getCampo('cod_logradouro') == $inCodLogradouroAnterior AND
                $rsRecordSet->getCampo('cod_municipio')  == $inCodMunicipioAnterior  AND
                $rsRecordSet->getCampo('cod_uf')         == $inCodUFAnterior
            )
        ){
            if ($inFirstLoop == true) {
                $arRecord[$inCount]['pagina'        ] = 0;
                $arRecord[$inCount]['sigla_uf'      ] = $rsRecordSet->getCampo('sigla_uf');
                $arRecord[$inCount]['nom_municipio' ] = $rsRecordSet->getCampo('nom_municipio');
                $arRecord[$inCount]['nom_tipo'      ] = $rsRecordSet->getCampo('nom_tipo');
                $arRecord[$inCount]['nom_logradouro'] = $rsRecordSet->getCampo('nom_logradouro');
                $arRecord[$inCount]['cod_logradouro'] = $rsRecordSet->getCampo('cod_logradouro');
            } else {
                $arRecord[$inCount]['pagina'        ] = 0;
                $arRecord[$inCount]['sigla_uf'      ] = "";
                $arRecord[$inCount]['nom_municipio' ] = "";
                $arRecord[$inCount]['nom_tipo'      ] = "";
                $arRecord[$inCount]['nom_logradouro'] = "";
                $arRecord[$inCount]['cod_logradouro'] = "";
            }
            $arRecord[$inCount]['bairros'] = $rsRecordSet->getCampo('nom_bairro');
            $arRecord[$inCount]['cep']     = array_key_exists($inCountLoop, $arCEP) ? trim($arCEP[$inCountLoop]) : '';

        } else {
            if ( $rsRecordSet->getCampo('cod_municipio') == $inCodMunicipioAnterior  AND $rsRecordSet->getCampo('cod_uf') == $inCodUFAnterior ) {
                $stNomMunicipio = '';
                $stNomUF        = '';
            } else {
                $stNomMunicipio = $rsRecordSet->getCampo('nom_municipio');
                $stNomUF        = $rsRecordSet->getCampo('sigla_uf');
            }
            $arRecord[$inCount]['pagina'        ] = 0;
            $arRecord[$inCount]['sigla_uf'      ] = $stNomUF;
            $arRecord[$inCount]['nom_municipio' ] = $stNomMunicipio;
            $arRecord[$inCount]['nom_tipo'      ] = $rsRecordSet->getCampo('nom_tipo');
            $arRecord[$inCount]['nom_logradouro'] = $rsRecordSet->getCampo('nom_logradouro');
            $arRecord[$inCount]['cod_logradouro'] = $rsRecordSet->getCampo('cod_logradouro');
            $arRecord[$inCount]['bairros']        = $rsRecordSet->getCampo('nom_bairro');
            $arRecord[$inCount]['cep']            = trim($arCEP[$inCountLoop]);
        }
        $inCodLogradouroAnterior = $rsRecordSet->getCampo('cod_logradouro');
        $inCodMunicipioAnterior  = $rsRecordSet->getCampo('cod_municipio' );
        $inCodUFAnterior         = $rsRecordSet->getCampo('cod_uf'        );
        $countCEPAnterior        = $countCEP;
        $inCount++;
        $inCountLoop++;
        $inFirstLoop = false;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
