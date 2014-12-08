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

    $Revision: 26504 $
    $Name$
    $Author: bruce $
    $Date: 2007-10-30 10:54:47 -0200 (Ter, 30 Out 2007) $

    * Casos de uso: uc-03.05.23
*/

/*
$Log$
Revision 1.2  2007/09/19 14:55:15  bruce
Ticket#10105#

Revision 1.1  2006/10/11 17:19:26  domluc
p/ Diegon:
   O componente de Contrato gera no formulario que o chama um buscainner e um span,
o buscainner somente aceita preenchimento via PopUp, ou seja, não é possivel digitar diretamente o numero do contrato.
   Chamando a popup do buscainner, ele devera poder filtrar por ( em ordem)
1) Número do Contrato ( inteiro)
2) Exercicio ( ref a Contrato) ( componente exercicio)
3) Modalidade ( combo)
4) Codigo da Licitação  ( inteiro )
5) Entidade ( componente)

entao o usuario clica em Ok, e o sistema exibe uma lista correspondente ao filtro informado.
o usuario seleciona um dos contratos na listageme o sistema fecha a popup, retornando ao formulario, onde o sistema preenche o numero do convenio e no
span criado pelo componente , exibe as informações recorrentes, que sao:
- exercicio
- modalidade
- licitação
- entidade
- cgm contratado

era isso

*/

require_once ( CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeGeral.class.php" );

class IPopUpContrato extends Objeto
{
    public $obBuscaInner;
    public $obSpanInfoAdicional;

    public function IPopUpContrato(&$obForm)
    {
        parent::Objeto();
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
        $this->obBuscaInner->setFuncaoBusca("abrePopUp('".CAM_GP_LIC_POPUPS."contrato/FLProcurarContrato.php','".$this->obBuscaInner->obForm->getName()."','".$this->obBuscaInner->obCampoCod->getName()."','".$this->obBuscaInner->getId()."','".$this->obBuscaInner->stTipoBusca."','".Sessao::getId()."','800','550');");

        $this->obBuscaInner->setValoresBusca( CAM_GP_LIC_POPUPS.'contrato/OCProcuraContrato.php?' .Sessao::getId(), $this->obBuscaInner->obForm->getName() );

        $this->obSpanInfoAdicional = new Span;
        $this->obSpanInfoAdicional->setId('spnInfoAdicional');

    }

    public function geraFormulario($obFormulario)
    {
        $obFormulario->addComponente    ( $this->obBuscaInner );
        $obFormulario->addSpan          ( $this->obSpanInfoAdicional );
    }

}
?>
