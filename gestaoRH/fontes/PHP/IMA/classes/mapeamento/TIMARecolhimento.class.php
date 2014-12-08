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
    * Classe de mapeamento da tabela ima.recolhimento
    * Data de Criação: 15/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.08.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.recolhimento
  * Data de Criação: 15/01/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMARecolhimento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMARecolhimento()
{
    parent::Persistente();
    $this->setTabela("ima.recolhimento");

    $this->setCampoCod('cod_recolhimento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_recolhimento','sequence',true  ,''     ,true,false);
    $this->AddCampo('descricao'       ,'varchar' ,true  ,'200'  ,false,false);

}
}
?>
