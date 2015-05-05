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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 11/03/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FMManterRegistroPrecoOrgaos.php 62200 2015-04-07 18:09:25Z michel $
  * $Date: 2015-04-07 15:09:25 -0300 (Ter, 07 Abr 2015) $
  * $Author: michel $
  * $Rev: 62200 $
  **/
  
$obHdnInId = new Hidden;
$obHdnInId->setName  ( 'inId' );
$obHdnInId->setId    ( 'inId' );
$obHdnInId->setValue ( $inId  );

$obTxtExercicio = new Exercicio();
$obTxtExercicio->setName('stExercicioOrgao');
$obTxtExercicio->setId('stExercicioOrgao');

$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo('*Unidade Executora');
$obIMontaUnidadeOrcamentaria->setValue( $stUnidadeOrcamentaria );
$obIMontaUnidadeOrcamentaria->setCodOrgao('');
$obIMontaUnidadeOrcamentaria->setCodUnidade('');
$obIMontaUnidadeOrcamentaria->setActionPosterior($pgProc);
$obIMontaUnidadeOrcamentaria->setNull(true);

$obRdoGerenciadorSim = new Radio();
$obRdoGerenciadorSim->setRotulo('*Orgão Gerenciador');
$obRdoGerenciadorSim->setName('inOrgaoGerenciador');
$obRdoGerenciadorSim->setId('inOrgaoGerenciador1');
$obRdoGerenciadorSim->setLabel("Sim");
$obRdoGerenciadorSim->setValue("1");
$obRdoGerenciadorSim->obEvento->setOnClick("montaParametrosGET('preencheNatureza');");

$obRdoGerenciadorNao = new Radio;
$obRdoGerenciadorNao->setName('inOrgaoGerenciador');
$obRdoGerenciadorNao->setId('inOrgaoGerenciador2');
$obRdoGerenciadorNao->setLabel("Não");
$obRdoGerenciadorNao->setValue("2");
$obRdoGerenciadorNao->obEvento->setOnClick("montaParametrosGET('retornaNatureza');");

$obRadioNaturezaProcedimentoParticipante = new Radio();
$obRadioNaturezaProcedimentoParticipante->setRotulo('*Natureza do Procedimento');
$obRadioNaturezaProcedimentoParticipante->setTitle('Os valores possíveis para identificar a Natureza do Procedimento de Adesão são:</br>
1 - Órgão Participante (órgão ou entidade que participa dos procedimentos iniciais do SRP e integra a Ata de Registro de Preços);</br>
2 - Órgão Não Participante (órgão ou entidade que não está contemplado na Ata de Registro de Preços).');
$obRadioNaturezaProcedimentoParticipante->setName('inNaturezaProcedimento');
$obRadioNaturezaProcedimentoParticipante->setId('inNaturezaProcedimento1');
$obRadioNaturezaProcedimentoParticipante->setLabel("Órgão Participante");
$obRadioNaturezaProcedimentoParticipante->setValue("1");
$obRadioNaturezaProcedimentoParticipante->setDisabled(false);

$obRadioNaturezaProcedimentoNaoParticipante = new Radio;
$obRadioNaturezaProcedimentoNaoParticipante->setName('inNaturezaProcedimento');
$obRadioNaturezaProcedimentoNaoParticipante->setId('inNaturezaProcedimento2');
$obRadioNaturezaProcedimentoNaoParticipante->setLabel("Órgão Não Participante");
$obRadioNaturezaProcedimentoNaoParticipante->setValue("2");
$obRadioNaturezaProcedimentoNaoParticipante->setDisabled(false);

$obTxtCodigoProcessoAdesao = new TextBox();
$obTxtCodigoProcessoAdesao->setName('stCodigoProcessoAdesao');
$obTxtCodigoProcessoAdesao->setId('stCodigoProcessoAdesao');
$obTxtCodigoProcessoAdesao->setRotulo('Nro. do Processo de Adesão');
$obTxtCodigoProcessoAdesao->setTitle('Número do processo de adesão do órgão à Ata de Registro de Preços.');
$obTxtCodigoProcessoAdesao->setMaxLength(17);
$obTxtCodigoProcessoAdesao->setMascara('999999999999/9999');
$obTxtCodigoProcessoAdesao->setValue( '' );
$obTxtCodigoProcessoAdesao->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+jQuery('#inCodEntidade').val()+'&stCodigoProcessoAdesao='+this.value,'validaNroAdesao');");

$obDtPublicacaoAvisoIntencao = new Data(); 
$obDtPublicacaoAvisoIntencao->setName('dtPublicacaoAvisoIntencao');
$obDtPublicacaoAvisoIntencao->setId('dtPublicacaoAvisoIntencao');
$obDtPublicacaoAvisoIntencao->setTitle ('Data de Publicação do Aviso de Intenção.');
$obDtPublicacaoAvisoIntencao->setRotulo('Data de Publicação do Aviso de Intenção');
$obDtPublicacaoAvisoIntencao->setValue( '' );

$obDtAdesao = new Data(); 
$obDtAdesao->setName('dtAdesao');
$obDtAdesao->setId('dtAdesao');
$obDtAdesao->setTitle ('Data da Adesão.');
$obDtAdesao->setRotulo('Data da Adesão');
$obDtAdesao->setValue( '' );

$obHdnIdOrgao = new Hidden();
$obHdnIdOrgao->setId('inHndIdOrgao');
$obHdnIdOrgao->setName('inHndIdOrgao');
$obHdnIdOrgao->setValue('');

$obBtnSalvarOrgao = new Button;
$obBtnSalvarOrgao->setName  ("btnSalvarOrgao");
$obBtnSalvarOrgao->setId    ("btnSalvarOrgao");
$obBtnSalvarOrgao->setValue ("Incluir Orgão");
$obBtnSalvarOrgao->setTipo  ("button");
$obBtnSalvarOrgao->obEvento->setOnClick("montaParametrosGET('incluirListaOrgaos');");

// Define Objeto Button para Limpar Item
$obBtnLimparOrgao = new Button;
$obBtnLimparOrgao->setValue( "Limpar" );
$obBtnLimparOrgao->obEvento->setOnClick("montaParametrosGET('limparFormOrgaos');");

# Table com Itens
$obSpanListaOrgao = new Span();
$obSpanListaOrgao->setID( 'spnListaOrgao' );
?>