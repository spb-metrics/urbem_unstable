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
    * Classe de mapeamento da tabela tcmgo.contrato_tipo
    * Data de Criação   : 03/09/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOContratoTipo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOContratoTipo()
{
    parent::Persistente();
    $this->setTabela("tcmgo".Sessao::getEntidade().".contrato_tipo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo');

    $this->AddCampo( 'cod_tipo'  , 'integer'  , true  , ''     , true , '');
    $this->AddCampo( 'descricao' , 'char'     , true  , '20'   , true , '');

}

}

?>
