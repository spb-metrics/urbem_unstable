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
  * Página de Processamento da Configuração Metas Fiscais
  * Data de Criação: 21/02/2014
  
  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  
  * @ignore
  *
  * $Id: PRManterConfiguracaoArquivoDCLRF.php 59612 2014-09-02 12:00:51Z gelson $
  
  * $Revision: 59612 $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
  
*/

include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoArquivoDCLRF.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoArquivoDCLRF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";


$stAcao = $request->get('stAcao');
$obErro = new Erro;

switch ($stAcao){
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $rsTTCEMGConfiguracaoArquivoDCLRF = new RecordSet();
        $obTTCEMGConfiguracaoArquivoDCLRF = new TTCEMGConfiguracaoArquivoDCLRF();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if(!$obErro->ocorreu())
        {
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('exercicio',$request->get('stExercicio'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('mes_referencia',$request->get('stMes'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_saldo_atual_concessoes_garantia',$request->get('flValorSaldoAtualConcessoesGarantia'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('receita_privatizacao',$request->get('flValorReceitaPrivatizacao'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_liquidado_incentivo_contribuinte',$request->get('flValorLiquidadoIncentivoContribuinte'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_liquidado_incentivo_instituicao_financeira',$request->get('flValorLiquidadoIncentivoInstituicaoFinanceiro'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_inscrito_rpnp_incentivo_contribuinte',$request->get('flValorInscritoRPNPIncentivoContribuinte'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_inscrito_rpnp_incentivo_instituicao_financeira',$request->get('flValorInscritoRPNPIncentivoInstituicaoFinanceiro'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_compromissado',$request->get('flValorCompromissado'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_recursos_nao_aplicados',$request->get('flValorRecursosNaoAplicados'));
            
            $obTTCEMGConfiguracaoArquivoDCLRF->recuperaPorChave($rsTTCEMGConfiguracaoArquivoDCLRF);
            
            if($rsTTCEMGConfiguracaoArquivoDCLRF->getNumLinhas() < 0)
            {
                $obErro = $obTTCEMGConfiguracaoArquivoDCLRF->inclusao($boTransacao);
            } else
            {
                $obErro = $obTTCEMGConfiguracaoArquivoDCLRF->alteracao($boTransacao);
            }

            if(!$obErro->ocorreu())
            {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId(),"Configuração de Dados Complementares à LRF","manter","aviso", Sessao::getId(), "../");
            }else
            {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
            
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoArquivoDCLRF);
        }
        
        break;
}

?>