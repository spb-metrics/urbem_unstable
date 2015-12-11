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
    * Monta estrutura de componentes para selecionar um contrato
    * Data de Criação: 11/10/2006

    * @author Analista: Lucas Teixeia Stephanou
    * @author Desenvolvedor: Lucas Teixeia Stephanou

    * @package URBEM
    * @subpackage

    $Id: IPopUpContrato.class.php 64081 2015-11-30 15:36:50Z michel $

    * Casos de uso: uc-03.05.23
*/

require_once CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeGeral.class.php";

class IPopUpContrato extends Objeto
{
    public $obBuscaInner;
    public $obHdnBoFornecedor;
    public $obSpanInfoAdicional;

    public function IPopUpContrato(&$obForm)
    {
        parent::Objeto();

        // Define Objeto Label para Fornecedor
        $this->obHdnBoFornecedor = new Hidden;
        $this->obHdnBoFornecedor->setName   ( "boFornecedor" );
        $this->obHdnBoFornecedor->setId     ( "boFornecedor" );
        $this->obHdnBoFornecedor->setValue  ( FALSE  );

        $this->obBuscaInner = new BuscaInner;
        $this->obBuscaInner->obForm = &$obForm;

        $this->obBuscaInner->setRotulo                ( 'Número do Contrato'                     );
        $this->obBuscaInner->setTitle                 ( 'Selecione o contrato na PopUp de busca' );
        $this->obBuscaInner->obCampoCod->setName      ( 'inNumContrato'                          );
        $this->obBuscaInner->obCampoCod->setId        ( 'inNumContrato'                          );
        $this->obBuscaInner->obCampoCod->setAlign     ( "left"                                   );
        $this->obBuscaInner->setId                    ( 'txtContrato'                            );
        $this->obBuscaInner->setNull                  ( true );
        $this->obBuscaInner->stTipoBusca = 'popup';
        $this->obBuscaInner->setFuncaoBusca("abrePopUp('".CAM_GP_LIC_POPUPS."contrato/FLProcurarContrato.php','".$this->obBuscaInner->obForm->getName()."','".$this->obBuscaInner->obCampoCod->getName()."','".$this->obBuscaInner->getId()."','".$this->obBuscaInner->stTipoBusca."','".Sessao::getId()."&boFornecedor='+jQuery('#boFornecedor').val(),'800','550');");
        $this->obBuscaInner->setValoresBusca( CAM_GP_LIC_POPUPS.'contrato/OCProcuraContrato.php?' .Sessao::getId(), $this->obBuscaInner->obForm->getName() );

        $this->obSpanInfoAdicional = new Span;
        $this->obSpanInfoAdicional->setId('spnInfoAdicional');

    }

    public function geraFormulario($obFormulario)
    {
        $obFormulario->addHidden        ( $this->obHdnBoFornecedor );
        $obFormulario->addComponente    ( $this->obBuscaInner );
        $obFormulario->addSpan          ( $this->obSpanInfoAdicional );
    }

}
?>
