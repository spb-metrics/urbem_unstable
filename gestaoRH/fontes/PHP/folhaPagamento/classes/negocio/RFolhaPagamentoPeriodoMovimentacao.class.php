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
* Classe de Regra de Negócio Folha Pagamento Periodo Movimentacao
* Data de Criação   : 24/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-02-13 13:27:10 -0200 (Qua, 13 Fev 2008) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacaoSituacao.class.php"           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                            );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                        );

class RFolhaPagamentoPeriodoMovimentacao
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodPeriodoMovimentacao;
    /**
    * @var Date
    * @access Private
    */
    public $dtInicial;
    /**
    * @var Date
    * @access Private
    */
    public $dtFinal;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoPeriodoContratoServidor;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoPeriodoContratoServidor;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoCalculoFolhaPagamento;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoCalculoFolhaPagamento;
    /**
    * @var Object
    * @access Private
    */
    public $obRFolhaPagamentoFolhaSituacao;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoFolhaComplementar;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoFolhaComplementar;
    /**
    * @var Boolean
    * @access Private
    */
    public $boTransacao;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodPeriodoMovimentacao($valor) { $this->inCodPeriodoMovimentacao = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setDtInicial($valor) { $this->dtInicial = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setDtFinal($valor) { $this->dtFinal = $valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoPeriodoContratoServidor($valor) { $this->arRFolhaPagamentoPeriodoContratoServidor = $valor; }
     /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoPeriodoContratoServidor(&$valor) { $this->roRFolhaPagamentoPeriodoContratoServidor = &$valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoCalculoFolhaPagamento($valor) { $this->arRFolhaPagamentoCalculoFolhaPagamento = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoCalculoFolhaPagamento(&$valor) { $this->roRFolhaPagamentoCalculoFolhaPagamento = &$valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRFolhaPagamentoFolhaSituacao($valor) { $this->obRFolhaPagamentoFolhaSituacao = $valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoFolhaComplementar($valor) { $this->arRFolhaPagamentoFolhaComplementar = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoFolhaComplementar(&$valor) { $this->roRFolhaPagamentoFolhaComplementar = &$valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao           = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTFolhaPagamentoPeriodoMovimentacao($valor) { $this->obTFolhaPagamentoPeriodoMovimentacao = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTFolhaPagamentoPeriodoMovimentacaoSituacao($valor) { $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao = $valor; }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodPeriodoMovimentacao() { return $this->inCodPeriodoMovimentacao; }
    /**
    * @access Public
    * @return Date
    */
    public function getDtInicial() { return $this->dtInicial; }
    /**
    * @access Public
    * @return Date
    */
    public function getDtFinal() { return $this->dtFinal;   }
    /**
    * @access Public
    * @return Array
    */
    public function getARRFolhaPagamentoPeriodoContratoServidor() { return $this->arRFolhaPagamentoPeriodoContratoServidor; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoPeriodoContratoServidor() { return $this->roRFolhaPagamentoPeriodoContratoServidor; }
    /**
    * @access Public
    * @return Array
    */
    public function getARRFolhaPagamentoCalculoFolhaPagamento() { return $this->arRFolhaPagamentoCalculoFolhaPagamento; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoCalculoFolhaPagamento() { return $this->roRFolhaPagamentoCalculoFolhaPagamento; }
    /**
    * @access Public
    * @return Object
    */
    public function getRFolhaPagamentoFolhaSituacao() { return $this->obRFolhaPagamentoFolhaSituacao; }
    /**
    * @access Public
    * @return Object
    */
    public function getARRFolhaPagamentoFolhaComplementar() { return $this->arRFolhaPagamentoFolhaComplementar; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoFolhaComplementar() { return $this->roRFolhaPagamentoFolhaComplementar; }

    /**
    * Método Construtor
    * @access Private
    */
    public function RFolhaPagamentoPeriodoMovimentacao()
    {
        $this->setTFolhaPagamentoPeriodoMovimentacao            ( new TFolhaPagamentoPeriodoMovimentacao            );
        $this->setTFolhaPagamentoPeriodoMovimentacaoSituacao    ( new TFolhaPagamentoPeriodoMovimentacaoSituacao    );
        $this->setTransacao                                     ( new Transacao                                     );
        $this->setRFolhaPagamentoFolhaSituacao                  ( new RFolhaPagamentoFolhaSituacao( $this )         );
    }

    public function listar(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsLista, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarPeriodoMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
    {
        if ( $this->getDtFinal() ) {
            $stFiltro .= "AND to_char(FPM.dt_final, 'yyyy-mm-dd')                    like '".$this->getDtFinal()."%' \n";
        }
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsLista, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarUltimaMovimentacao(&$rsUltimaMovimentacao, $boTransacao = "")
    {
        $stFiltro = "";
        $stOrdem = "";
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarUltimaMovimentacaoFechada(&$rsUltimaMovimentacao, $boTransacao = "")
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacaoFechada($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function abrirPeriodoMovimentacao($boTransacao = "")
    {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "dt_inicial"              , $this->getDtInicial() );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "dt_final"                , $this->getDtFinal()   );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "exercicio"               , Sessao::getExercicio()    );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "cod_entidade"            , Sessao::getEntidade()    );
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->abrirPeriodoMovimentacao($boTransacao);
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacao );

        return $obErro;
    }

    public function fecharPeriodoMovimentacao($boTransacao = "")
    {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->setDado( "cod_periodo_movimentacao", $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') );
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->setDado( "situacao", "f" );
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->inclusao($boTransacao);
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao );

        return $obErro;
    }

    public function cancelarPeriodoMovimentacao($boTransacao = "")
    {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "cod_entidade", Sessao::getEntidade()    );
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->cancelarPeriodoMovimentacao($boTransacao);
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacao );

        return $obErro;
    }

    public function addRFolhaPagamentoPeriodoContratoServidor()
    {
        $this->arRFolhaPagamentoPeriodoContratoServidor[] = new RFolhaPagamentoPeriodoContratoServidor( $this );
        $this->roRFolhaPagamentoPeriodoContratoServidor = &$this->arRFolhaPagamentoPeriodoContratoServidor[ count($this->arRFolhaPagamentoPeriodoContratoServidor)-1 ];
    }

    public function addRFolhaPagamentoCalculoFolhaPagamento()
    {
        //$this->arRFolhaPagamentoCalculoFolhaPagamento[] = new RFolhaPagamentoCalculoFolhaPagamento();
        //$this->roRFolhaPagamentoCalculoFolhaPagamento = &$this->arRFolhaPagamentoCalculoFolhaPagamento[ count($this->arRFolhaPagamentoCalculoFolhaPagamento)-1 ];
        $this->roRFolhaPagamentoCalculoFolhaPagamento = new RFolhaPagamentoCalculoFolhaPagamento();
        $this->roRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao($this);
    }

    public function addRFolhaPagamentoFolhaComplementar()
    {
        $this->arRFolhaPagamentoFolhaComplementar[] = new RFolhaPagamentoFolhaComplementar( $this );
        $this->roRFolhaPagamentoFolhaComplementar = &$this->arRFolhaPagamentoFolhaComplementar[ count($this->arRFolhaPagamentoFolhaComplementar)-1 ];
    }

    public function recuperaAnosPeriodoMovimentacao(&$rsUltimaMovimentacao, $stFiltro, $boTransacao = '')
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaAnosPeriodoMovimentacao($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

}
