<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Solu��es em Gest�o P�blica                                *
    * @copyright (c) 2013 Confedera��o Nacional de Munic�pos                         *
    * @author Confedera��o Nacional de Munic�pios                                    *
    *                                                                                *
    * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo  sob *
    * os termos da Licen�a P�blica Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio) qualquer vers�o *
    *                                                                                *
    * Este  programa  �  distribu�do  na  expectativa  de  que  seja  �til,   por�m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia impl�cita  de  COMERCIABILIDADE  OU *
    * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral  do  GNU  junto  com *
    * este programa; se n�o, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**

  * Efetua conex�o com a tabela  licitacao.natureza_cargo
  * Data de Cria��o: 04/04/2014

  * @author Analista: Gelson W. Gon�alves
  * @author Desenvolvedor: Lisiane Morais

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 17482 $
    $Name$
    
    $Id: TLicitacaoNaturezaCargo.class.php 59612 2014-09-02 12:00:51Z gelson $
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );


class TLicitacaoNaturezaCargo extends Persistente
{
/**
    * M�todo Construtor
    * @access Private
*/
function TLicitacaoNaturezaCargo()
{
    parent::Persistente();
    $this->setTabela("licitacao.natureza_cargo");

    $this->setCampoCod('codigo');
    $this->setComplementoChave('');

    $this->AddCampo('codigo','sequence',false ,''    ,true,false);
    $this->AddCampo('descricao','varchar' ,false ,'20'  ,false,false);

}
}
