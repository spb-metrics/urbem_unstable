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
    * Classe de Regra de Negócio Tipo Admissao
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalFormaPagamento.class.php"                           );

/**
    * Classe de Regra de Negócio Pesssoal Tipo Admissao
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalFormaPagamento
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodFormaPagamento;

    /**
    * @var String
    * @access Private
    */
    public $stDescricao;

    /**
    * @var Object
    * @access Private
    */
    public $obTPessoalFormaPagamento;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodFormaPagamento($valor) { $this->inCodFormaPagamento          = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDescricao($valor) { $this->stDescricao                  = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalFormaPagamento($valor) { $this->obTPessoalFormaPagamento       = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodFormaPagamento() { return $this->inCodFormaPagamento;         }
    public function getDescricao() { return $this->stDescricao;                 }
    public function getTPessoalFormaPagamento() { return $this->obTPessoalFormaPagamento;    }

    public function RPessoalFormaPagamento()
    {
        $this->setTPessoalFormaPagamento           ( new TPessoalFormaPagamento          );

    }

    /**
    * Executa um recuperaTodos na classe Persistente PessoalFormaPagamento
    * @access Public
    * @param  Object $rsResultado Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarFormaPagamento(&$rsResultado , $stFiltro = "", $boTransacao = "")
    {
        $obErro = $this->obTPessoalFormaPagamento->recuperaTodos( $rsResultado, $stFiltro, "descricao", $boTransacao );

        return $obErro;
    }

}
