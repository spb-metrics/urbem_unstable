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

/*
 * Classe de mapeamento da tabela tcepe.tipo_conta_corrente
 * Data de Criação: 13/10/2014
 * @author Desenvolvedor: Evandro Melos
 * @package URBEM
 * @subpackage
 $Id: TTCEPETipoContaCorrente.class.php 60330 2014-10-14 16:30:00Z evandro $
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEPETipoContaCorrente extends Persistente
{
    /**
     * Método Construtor da classe de mapeamento
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela  ('tcepe.tipo_conta_corrente');

        $this->setCampoCod('cod_tipo');
        
        $this->AddCampo('cod_tipo'        , 'integer', true, ''  , true  , true);
        $this->AddCampo('descricao'       , 'varchar', true, '20', false , false);
    }

}