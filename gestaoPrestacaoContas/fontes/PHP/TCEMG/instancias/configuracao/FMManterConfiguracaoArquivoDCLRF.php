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
  * Página de Formulario de Configuração de IDE
  * Data de Criação: 20/02/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  * $Id: FMManterConfiguracaoArquivoDCLRF.php 59612 2014-09-02 12:00:51Z gelson $
  *   
  * $Rev: 59612 $
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

$rsTTCEMGConfiguracaoArquivoDCLRF = new RecordSet();
$obTTCEMGConfiguracaoArquivoDCLRF = new TTCEMGConfiguracaoArquivoDCLRF();
$obTTCEMGConfiguracaoArquivoDCLRF->setDado('exercicio',$request->get('inExercicio'));
$obTTCEMGConfiguracaoArquivoDCLRF->setDado('mes_referencia',$request->get('inMes'));
$obTTCEMGConfiguracaoArquivoDCLRF->recuperaValoresArquivoDCLRF($rsTTCEMGConfiguracaoArquivoDCLRF);

if($rsTTCEMGConfiguracaoArquivoDCLRF->getNumLinhas() > 0)
{
    $vlSaldoAtualConcessoesGarantia               = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_concessoes_garantia'), '2', ',', '.');
    $vlReceitaPrivatizacao                        = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('receita_privatizacao'), '2', ',', '.');
    $vlLiquidadoIncentivoContribuinte             = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_liquidado_incentivo_contribuinte'), '2', ',', '.');
    $vlLiquidadoIncentivoInstituicaoFinanceiro    = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_liquidado_incentivo_instituicao_financeira'), '2', ',', '.');
    $vlInscritoRPNPIncentivoContribuinte          = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_inscrito_rpnp_incentivo_contribuinte'), '2', ',', '.');
    $vlInscritoRPNPIncentivoInstituicaoFinanceiro = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_inscrito_rpnp_incentivo_instituicao_financeira'), '2', ',', '.');
    $vlCompromissado                              = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_compromissado'), '2', ',', '.');
    $vlRecursosNaoAplicados                       = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_recursos_nao_aplicados'), '2', ',', '.');

} else
{
    $vlSaldoAtualConcessoesGarantia = "0,00";
    $vlReceitaPrivatizacao = "0,00";
    $vlLiquidadoIncentivoContribuinte = "0,00";
    $vlLiquidadoIncentivoInstituicaoFinanceiro = "0,00";
    $vlInscritoRPNPIncentivoContribuinte = "0,00";
    $vlInscritoRPNPIncentivoInstituicaoFinanceiro = "0,00";
    $vlCompromissado = "0,00";
    $vlRecursosNaoAplicados = "0,00";

}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnAno = new Hidden();
$obHdnAno->setId   ('stExercicio');
$obHdnAno->setName ('stExercicio');
$obHdnAno->setValue($request->get('inExercicio'));

$obHdnMes = new Hidden();
$obHdnMes->setId   ('stMes');
$obHdnMes->setName ('stMes');
$obHdnMes->setValue($request->get('inMes'));

$obFlValorSaldoAtualConcessoesGarantia = new Numerico();
$obFlValorSaldoAtualConcessoesGarantia->setId('flValorSaldoAtualConcessoesGarantia');
$obFlValorSaldoAtualConcessoesGarantia->setName('flValorSaldoAtualConcessoesGarantia');
$obFlValorSaldoAtualConcessoesGarantia->setRotulo('Saldo atual das concessões de garantia');
$obFlValorSaldoAtualConcessoesGarantia->setTitle('Saldo atual das concessões de garantia decorrentes do compromisso de adimplência de obrigação financeira ou contratual assumida por Ente da Federação ou entidade a ele vinculada (art. 29, IV, da Lei de Responsabilidade Fiscal).');
$obFlValorSaldoAtualConcessoesGarantia->setDecimais(2);
$obFlValorSaldoAtualConcessoesGarantia->setMaxLength(15);
$obFlValorSaldoAtualConcessoesGarantia->setSize(17);
$obFlValorSaldoAtualConcessoesGarantia->setValue($vlSaldoAtualConcessoesGarantia);

$obFlValorReceitaPrivatizacao = new Numerico();
$obFlValorReceitaPrivatizacao->setId('flValorReceitaPrivatizacao');
$obFlValorReceitaPrivatizacao->setName('flValorReceitaPrivatizacao');
$obFlValorReceitaPrivatizacao->setRotulo('Receita de Privatização');
$obFlValorReceitaPrivatizacao->setTitle('Valores correspondente a Receita de Privatização.<br/>
 Registrar o valor arrecadado da Receita de Privatizações, subtraído das despesas de vendas (impostos de renda sobre a operação, comissão de venda e gastos com avaliação e reestruturação da empresa) e acrescido das dívidas transferidas identificadas no sistema financeiro.');
$obFlValorReceitaPrivatizacao->setDecimais(2);
$obFlValorReceitaPrivatizacao->setMaxLength(15);
$obFlValorReceitaPrivatizacao->setSize(17);
$obFlValorReceitaPrivatizacao->setValue($vlReceitaPrivatizacao);

$obFlValorLiquidadoIncentivoContribuinte = new Numerico();
$obFlValorLiquidadoIncentivoContribuinte->setId('flValorLiquidadoIncentivoContribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setName('flValorLiquidadoIncentivoContribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setRotulo('Valor Liquidado de Incentivo a Contribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setTitle('Registrar as despesas de capital liquidadas sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, tendo por base tributo de competência do ente da Federação, se resultar na diminuição, direta ou indireta, do ônus do ente (art. 32, §3o, inciso I da LRF).');
$obFlValorLiquidadoIncentivoContribuinte->setDecimais(2);
$obFlValorLiquidadoIncentivoContribuinte->setMaxLength(15);
$obFlValorLiquidadoIncentivoContribuinte->setSize(17);
$obFlValorLiquidadoIncentivoContribuinte->setValue($vlLiquidadoIncentivoContribuinte);

$obFlValorLiquidadoIncentivoInstituicaoFinanceiro = new Numerico();
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setId('flValorLiquidadoIncentivoInstituicaoFinanceiro');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setName('flValorLiquidadoIncentivoInstituicaoFinanceiro');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setRotulo('Valor Liquidado de Incentivo concedido por Instituição Financeira');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setTitle('Registrar as despesas de capital liquidadas sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, concedido por instituição financeira controlada pelo ente da Federação(art. 32, § 3o, inciso II da LRF).');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setDecimais(2);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setMaxLength(15);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setSize(17);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setValue($vlLiquidadoIncentivoInstituicaoFinanceiro);

$obFlValorInscritoRPNPIncentivoContribuinte = new Numerico();
$obFlValorInscritoRPNPIncentivoContribuinte->setId('flValorInscritoRPNPIncentivoContribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setName('flValorInscritoRPNPIncentivoContribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setRotulo('Valor Inscrito em Restos a Pagar Não Processados de Incentivo a Contribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setTitle('Registrar as despesas de capital inscritas em Restos a Pagar Não Processados sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, tendo por base tributo de competência do ente da Federação, se resultar na diminuição, direta ou indireta, do ônus do ente (art. 32, § 3o, inciso I da LRF).');
$obFlValorInscritoRPNPIncentivoContribuinte->setDecimais(2);
$obFlValorInscritoRPNPIncentivoContribuinte->setMaxLength(15);
$obFlValorInscritoRPNPIncentivoContribuinte->setSize(17);
$obFlValorInscritoRPNPIncentivoContribuinte->setValue($vlInscritoRPNPIncentivoContribuinte);

$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro = new Numerico();
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setId('flValorInscritoRPNPIncentivoInstituicaoFinanceiro');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setName('flValorInscritoRPNPIncentivoInstituicaoFinanceiro');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setRotulo('Valor Inscrito em Restos a Pagar Não Processados de Incentivo concedido por Instituição Financeira');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setTitle('Registrar as despesas de capital inscritas em Restos a Pagar Não Processados sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, concedido por instituição financeira controlada pelo ente da Federação (art. 32, § 3o, inciso II da LRF).');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setDecimais(2);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setMaxLength(15);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setSize(17);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setValue($vlInscritoRPNPIncentivoInstituicaoFinanceiro);

$obFlValorCompromissado = new Numerico();
$obFlValorCompromissado->setId('flValorCompromissado');
$obFlValorCompromissado->setName('flValorCompromissado');
$obFlValorCompromissado->setRotulo('Total dos valores compromissados (Passivo Financeiro)');
$obFlValorCompromissado->setTitle('Total dos valores compromissados (Passivo Financeiro).<br/>
OBS: Incluem-se o saldo atual negativo apurado da conta devedores diversos do ativo financeiro.');
$obFlValorCompromissado->setDecimais(2);
$obFlValorCompromissado->setMaxLength(15);
$obFlValorCompromissado->setSize(17);
$obFlValorCompromissado->setValue($vlCompromissado);

$obFlValorRecursosNaoAplicados = new Numerico();
$obFlValorRecursosNaoAplicados->setId('flValorRecursosNaoAplicados');
$obFlValorRecursosNaoAplicados->setName('flValorRecursosNaoAplicados');
$obFlValorRecursosNaoAplicados->setRotulo('Recursos do FUNDEB não aplicados no exercício anterior (§2° do art. 21, lei 11.494/2007)');
$obFlValorRecursosNaoAplicados->setTitle('Recursos do FUNDEB não aplicados no exercício anterior (§2° do art. 21, lei 11.494/2007).<br/>
OBS: Deve ser informado somente no mês de janeiro e apenas pelo órgão "02 - Prefeitura Municipal".');
$obFlValorRecursosNaoAplicados->setDecimais(2);
$obFlValorRecursosNaoAplicados->setMaxLength(15);
$obFlValorRecursosNaoAplicados->setSize(17);
$obFlValorRecursosNaoAplicados->setValue($vlRecursosNaoAplicados);
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnAno );
$obFormulario->addHidden       ( $obHdnMes );
$obFormulario->setLarguraRotulo( 40 );
$obFormulario->setLarguraCampo ( 60 );
$obFormulario->addTitulo       ( "Configuração do Arquivo Dados Complementares à LRF" );
$obFormulario->addComponente   ( $obFlValorSaldoAtualConcessoesGarantia );
$obFormulario->addComponente   ( $obFlValorReceitaPrivatizacao );
$obFormulario->addComponente   ( $obFlValorLiquidadoIncentivoContribuinte );
$obFormulario->addComponente   ( $obFlValorLiquidadoIncentivoInstituicaoFinanceiro );
$obFormulario->addComponente   ( $obFlValorInscritoRPNPIncentivoContribuinte );
$obFormulario->addComponente   ( $obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro );
$obFormulario->addComponente   ( $obFlValorCompromissado );
$obFormulario->addComponente   ( $obFlValorRecursosNaoAplicados );

$obFormulario->OK();
$obFormulario->show();

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php");
?>