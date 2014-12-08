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
 * Classe Mapeameto do 02.10.01 - Homologar LDO
 * Data de Criação: 06/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.01 - Homologar LDO
 */

class TLDOHomologacaoLDO extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.homologacao');

        $this->setCampoCod('ano');

        $this->addCampo('ano', 'character', true, '4', true, true);
        $this->addCampo('cod_norma', 'integer', true, '', false, true);
        $this->addCampo('cod_processo', 'integer', true, '', false, true);
        $this->addCampo('ano_exercicio', 'character', true, '4', false, true);
        $this->addCampo('numcgm_veiculo', 'integer', true, '', false, true);
        $this->addCampo('dt_encaminhamento', 'date', true, '', false, false);
        $this->addCampo('dt_devolucao', 'date', true, '', false, false);
    }
}
