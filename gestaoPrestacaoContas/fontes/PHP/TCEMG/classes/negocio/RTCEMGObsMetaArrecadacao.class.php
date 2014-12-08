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
   /*
    * Regra de negócio do arquivo obsMetaArrecadacao.txt
    * Data de Criação   : 21/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGObsMetaArrecadacao.class.php" );

class RTCEMGObsMetaArrecadacao
{
    public $obTransacao,
        $obTTCEMGExecucaoVariacao;

    public function __construct()
    {
        $this->obTransacao                = new Transacao               ();
        $this->obTTCEMGObsMetaArrecadacao = new TTCEMGObsMetaArrecadacao();
    }

    public function incluirObsMetaArrecadacao($arItens, $boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'exercicio'     , Sessao::getExercicio()     );
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'mes'           , $arItens['inMes']          );
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'observacao'    , trim($arItens['stObserv']) );
        $obErro = $this->obTTCEMGObsMetaArrecadacao->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Observações já esxistentes para o mês '.$arItens['inMes'].'/'.Sessao::getExercicio().'.' );
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTCEMGObsMetaArrecadacao);

        return $obErro;
    }

    public function alterarObsMetaArrecadacao($arItens, $boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'exercicio'     , Sessao::getExercicio()     );
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'mes'           , $arItens['inMes']          );
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'observacao'    , trim($arItens['stObserv']) );
        $obErro = $this->obTTCEMGObsMetaArrecadacao->alteracao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTCEMGObsMetaArrecadacao);

        return $obErro;
    }

    public function consultaObsMetaArrecadacao($arItens)
    {
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'exercicio', Sessao::getExercicio() );
        $this->obTTCEMGObsMetaArrecadacao->setDado( 'mes'      , $arItens['inMes']      );
        $this->obTTCEMGObsMetaArrecadacao->recuperaPorChave($rsRecordSet);

        return $rsRecordSet;
    }

}
?>
