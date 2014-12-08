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
* Arquivo de popup de busca de Recurso
* Data de Criação: 20/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

* $Id: IPopUpContaBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class  IPopUpContaBanco extends BuscaInner
{

    public $obCmbEntidades;

    public function IPopUpContaBanco($obCmbEntidades = "")
    {

        parent::BuscaInner();

        $this->obCmbEntidades = $obCmbEntidades;

        $this->setRotulo( "Conta Banco" );
        $this->setTitle( "Informe a conta banco." );
        $this->setNull( false );
        $this->setId( "stNomContaBanco" );
        $this->setValue( '' );
        $this->obCampoCod->setName("inCodContaBanco");
        $this->obCampoCod->setSize     ( 10 );
        $this->obCampoCod->setValue( "" );

    }

    public function montaHTML()
    {
        if ($this->obCmbEntidades) {
            if ( strtolower(get_class( $this->obCmbEntidades )) == "select" ) {
                $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCContaBanco.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S'";
                $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
                $this->setFuncaoBusca ( "if(document.frm.".$this->obCmbEntidades->getName().".value) abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','banco&inCodEntidade='+document.frm.".$this->obCmbEntidades->getName().".value,'".Sessao::getId()."','800',''); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
            } else {
                $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCContaBanco.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=S'";
                $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2()."),'buscaPopup');" );
                $this->setFuncaoBusca ( "if(document.frm.".$this->obCmbEntidades->getNomeLista2()."[0]) abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','banco&'+selectMultiploToString(".$this->obCmbEntidades->getNomeLista2().")+'&stNomSelectMultiplo=".$this->obCmbEntidades->getNomeLista2()."','".Sessao::getId()."','800',''); else alertaAviso('É necessário informar uma entidade para a conta.','frm','erro','".Sessao::getId()."');");
            }
        } else {
            $pgOcul = "'".CAM_GF_CONT_PROCESSAMENTO."OCContaBanco.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stUsaEntidade=N'";
            $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
            $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','tes_pagamento','".Sessao::getId()."','800','');");
        }
        parent::montaHTML();
    }
}
?>
