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
    * Classe de regra de negócio dos assentamentos gerados.
    * Data de Criação: 30/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGeracaoAssentamento.class.php"                          );

class RPessoalAssentamentoGeradoContratoServidor
{
/**
    * @access Private
    * @var Array
*/
var $arRPessoalGeracaoAssentamento;
/**
    * @access Private
    * @var Object
*/
var $roRPessoalGeracaoAssentamento;

/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalAssentamentoGeradoContratoServidor($valor) { $this->arRPessoalGeracaoAssentamento  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalAssentamentoGeradoContratoServidor(&$valor) { $this->roRPessoalGeracaoAssentamento = &$valor; }

/**
    * @access Public
    * @return Array
*/
function getARRPessoalAssentamentoGeradoContratoServidor() { return $this->arRPessoalGeracaoAssentamento;  }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalAssentamentoGeradoContratoServidor() { return $this->roRPessoalGeracaoAssentamento;  }

/**
    * Método construtor
    * @access Private
*/
function RPessoalAssentamentoGeradoContratoServidor()
{
}

/**
    * Adiciona um GeracaoAssentamento ao objeto
    * @access Public
    * @param  Object $obTransacao
*/
function addRPessoalGeracaoAssentamento($boTransacao = "")
{
    $this->roRPessoalGeracaoAssentamento   = new RPessoalGeracaoAssentamento();
    $this->arRPessoalGeracaoAssentamento[] = $this->roRPessoalGeracaoAssentamento;
}

/**
    * Grava no banco de dados todos os assentamentos gerados
    * @access Public
    * @param  Object $obTransacao
    * @return Object Objeto Erro
*/
function incluirAssentamentoGeradoContratoServidor($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        for ( $inIndex=0 ; $inIndex<count($this->arRPessoalGeracaoAssentamento) ; $inIndex++ ) {
            $obRPessoalGeracaoAssentamento = $this->arRPessoalGeracaoAssentamento[$inIndex];
            $obErro = $obRPessoalGeracaoAssentamento->incluirGeracaoAssentamento( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalConselho );

    return $obErro;
}
}
?>
