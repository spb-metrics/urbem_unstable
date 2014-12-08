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
  * Classe de mapeamento da tabela ECONOMICO.ATIVIDADE_MODALIDADE_MOEDA
  * Data de Criação: 18/10/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fabio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividadeModalidadeMoeda.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.1  2006/11/08 10:34:36  fabio
alteração do uc_05.02.13

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMAtividadeModalidadeMoeda extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividadeModalidadeMoeda()
{
    parent::Persistente();
    $this->setTabela('economico.atividade_modalidade_moeda');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atividade,cod_modalidade,dt_inicio,cod_moeda');

    $this->AddCampo('cod_atividade' ,'integer',true ,''    ,true ,true );
    $this->AddCampo('cod_modalidade','integer',true ,''    ,true ,true );
    $this->AddCampo('dt_inicio'     ,'date'   ,true ,''    ,true ,false);
    $this->AddCampo('cod_moeda'     ,'integer',true ,''    ,true ,true );
}

}
